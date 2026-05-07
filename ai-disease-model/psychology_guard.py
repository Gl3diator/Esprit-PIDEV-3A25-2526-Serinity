def is_psychological_prompt(scope_model, prompt: str, threshold: float = 0.20) -> bool:
    """
    Decides if a prompt is psychological using the trained scope model.
    More flexible than strict class prediction.
    """

    if not prompt or prompt.strip() == "":
        return False

    if hasattr(scope_model, "predict_proba"):
        classes = list(scope_model.classes_)

        if "psychological" not in classes:
            return False

        psychological_index = classes.index("psychological")

        psychological_probability = float(
            scope_model.predict_proba([prompt])[0][psychological_index]
        )

        return psychological_probability >= threshold

    scope_prediction = scope_model.predict([prompt])[0]

    return scope_prediction == "psychological"