from __future__ import annotations

from pathlib import Path
from typing import Any

import joblib
from flask import Flask, jsonify, request
from flask_cors import CORS

from psychology_guard import (
    get_psychological_probability,
    is_psychological_prompt,
    normalize_prompt_text,
)


BASE_DIR = Path(__file__).resolve().parent
MODEL_PATH = BASE_DIR / "model" / "psychological_ai_bundle.pkl"
DEFAULT_SCOPE_THRESHOLD = 0.20
DEFAULT_HOST = "127.0.0.1"
DEFAULT_PORT = 5001


def load_bundle(model_path: Path) -> dict[str, Any]:
    bundle = joblib.load(model_path)

    if "scope_model" not in bundle or "disease_model" not in bundle:
        raise ValueError("Model bundle must contain 'scope_model' and 'disease_model'.")

    return bundle


def get_disease_prediction_details(model: Any, prompt: str) -> tuple[str, float | None, list[dict[str, float | str]]]:
    normalized_prompt = normalize_prompt_text(prompt)
    predicted_label = str(model.predict([normalized_prompt])[0])

    if not hasattr(model, "predict_proba"):
        return predicted_label, None, []

    probabilities = model.predict_proba([normalized_prompt])[0]
    classes = [str(label) for label in model.classes_]
    ranked_predictions = sorted(
        zip(classes, probabilities),
        key=lambda item: item[1],
        reverse=True,
    )

    top_predictions = [
        {
            "label": label,
            "probability": round(float(probability), 4),
        }
        for label, probability in ranked_predictions[:3]
    ]

    disease_confidence = next(
        (
            round(float(probability), 4)
            for label, probability in ranked_predictions
            if label == predicted_label
        ),
        None,
    )

    return predicted_label, disease_confidence, top_predictions


def create_error_response(message: str, medical_scope: str, status_code: int) -> tuple[Any, int]:
    return jsonify(
        {
            "success": False,
            "message": message,
            "mentionedText": "",
            "probableDisease": None,
            "medicalScope": medical_scope,
        }
    ), status_code


bundle = load_bundle(MODEL_PATH)
scope_model = bundle["scope_model"]
disease_model = bundle["disease_model"]
scope_threshold = float(bundle.get("scope_threshold", DEFAULT_SCOPE_THRESHOLD))
disease_confidence_threshold = float(bundle.get("disease_confidence_threshold", 0.35))

app = Flask(__name__)
CORS(app)


@app.route("/health", methods=["GET"])
def health():
    return jsonify(
        {
            "status": "ok",
            "message": "Psychological AI API is running",
            "modelLoaded": True,
            "scopeThreshold": scope_threshold,
        }
    )


@app.route("/predict-from-prompt", methods=["POST"])
def predict_from_prompt():
    try:
        data = request.get_json(silent=True)
        if not isinstance(data, dict):
            return create_error_response("Invalid JSON body.", "invalid_request", 400)

        prompt = str(data.get("prompt", "")).strip()
        normalized_prompt = normalize_prompt_text(prompt)

        if normalized_prompt == "":
            return jsonify(
                {
                    "success": False,
                    "message": "Please describe what you are feeling.",
                    "mentionedText": "",
                    "probableDisease": None,
                    "medicalScope": "empty",
                }
            ), 400

        scope_confidence = round(
            get_psychological_probability(scope_model, prompt),
            4,
        )

        if not is_psychological_prompt(scope_model, prompt, scope_threshold):
            return jsonify(
                {
                    "success": False,
                    "message": "This assistant only answers psychological symptom prompts.",
                    "mentionedText": prompt,
                    "probableDisease": None,
                    "medicalScope": "non_psychological",
                    "scopeConfidence": scope_confidence,
                }
            ), 200

        probable_disease, disease_confidence, top_predictions = get_disease_prediction_details(
            disease_model,
            normalized_prompt,
        )

        prediction_quality = "high"
        if disease_confidence is not None and disease_confidence < disease_confidence_threshold:
            prediction_quality = "low"
        elif disease_confidence is not None and disease_confidence < 0.60:
            prediction_quality = "medium"

        return jsonify(
            {
                "success": True,
                "mentionedText": prompt,
                "normalizedText": normalized_prompt,
                "probableDisease": probable_disease,
                "medicalScope": "psychological_only",
                "scopeConfidence": scope_confidence,
                "diseaseConfidence": disease_confidence,
                "predictionQuality": prediction_quality,
                "topPredictions": top_predictions,
            }
        )

    except Exception as error:
        return jsonify(
            {
                "success": False,
                "message": str(error),
                "mentionedText": "",
                "probableDisease": None,
                "medicalScope": "error",
            }
        ), 500


if __name__ == "__main__":
    app.run(
        host=DEFAULT_HOST,
        port=DEFAULT_PORT,
        debug=True,
    )
