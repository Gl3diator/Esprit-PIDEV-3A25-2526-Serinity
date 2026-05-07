import os
import joblib

from flask import Flask, jsonify, request
from flask_cors import CORS

from psychology_guard import is_psychological_prompt


MODEL_PATH = os.path.join("model", "psychological_ai_bundle.pkl")

app = Flask(__name__)
CORS(app)

bundle = joblib.load(MODEL_PATH)

scope_model = bundle["scope_model"]
disease_model = bundle["disease_model"]

# Lower threshold = more flexible for real user prompts
scope_threshold = 0.20


@app.route("/health", methods=["GET"])
def health():
    return jsonify({
        "status": "ok",
        "message": "Psychological AI API is running"
    })


@app.route("/predict-from-prompt", methods=["POST"])
def predict_from_prompt():
    try:
        data = request.get_json(force=True)
        prompt = str(data.get("prompt", "")).strip()

        if prompt == "":
            return jsonify({
                "success": False,
                "message": "Please describe what you are feeling.",
                "mentionedText": "",
                "probableDisease": None,
                "medicalScope": "empty"
            }), 400

        if not is_psychological_prompt(scope_model, prompt, scope_threshold):
            return jsonify({
                "success": False,
                "message": "This assistant only answers psychological symptom prompts.",
                "mentionedText": prompt,
                "probableDisease": None,
                "medicalScope": "non_psychological"
            }), 200

        probable_disease = disease_model.predict([prompt])[0]

        return jsonify({
            "success": True,
            "mentionedText": prompt,
            "probableDisease": probable_disease,
            "medicalScope": "psychological_only"
        })

    except Exception as error:
        return jsonify({
            "success": False,
            "message": str(error),
            "mentionedText": "",
            "probableDisease": None,
            "medicalScope": "error"
        }), 500


if __name__ == "__main__":
    app.run(
        host="127.0.0.1",
        port=5001,
        debug=True
    )