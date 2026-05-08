from __future__ import annotations

import re
from typing import Any


def normalize_prompt_text(prompt: str) -> str:
    normalized = str(prompt).lower().replace("_", " ").strip()
    normalized = re.sub(r"[^a-z0-9\s']", " ", normalized)
    normalized = re.sub(r"\s+", " ", normalized)

    return normalized


def get_psychological_probability(scope_model: Any, prompt: str) -> float:
    normalized_prompt = normalize_prompt_text(prompt)

    if normalized_prompt == "":
        return 0.0

    if not hasattr(scope_model, "predict_proba"):
        scope_prediction = scope_model.predict([normalized_prompt])[0]
        return 1.0 if scope_prediction == "psychological" else 0.0

    classes = [str(label) for label in scope_model.classes_]

    if "psychological" not in classes:
        return 0.0

    psychological_index = classes.index("psychological")

    return float(scope_model.predict_proba([normalized_prompt])[0][psychological_index])


def is_psychological_prompt(scope_model: Any, prompt: str, threshold: float = 0.20) -> bool:
    """
    Decides if a prompt is psychological using the trained scope model.
    More flexible than strict class prediction.
    """

    return get_psychological_probability(scope_model, prompt) >= threshold
