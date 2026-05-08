from __future__ import annotations

from pathlib import Path

import joblib
import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.linear_model import LogisticRegression
from sklearn.metrics import accuracy_score, classification_report, fbeta_score
from sklearn.model_selection import train_test_split
from sklearn.pipeline import Pipeline

from psychology_guard import normalize_prompt_text


BASE_DIR = Path(__file__).resolve().parent
MODEL_DIR = BASE_DIR / "model"
MODEL_PATH = MODEL_DIR / "psychological_ai_bundle.pkl"
DATASET_PATHS = [
    BASE_DIR / "Final_Augmented_dataset_Diseases_and_Symptoms.csv",
    BASE_DIR / "Final_Augmented_dataset_Diseases_and_Symptoms",
]
RANDOM_STATE = 42
SCOPE_THRESHOLD = 0.20
MIN_SAMPLES_PER_DISEASE = 2
DEFAULT_DISEASE_CONFIDENCE_THRESHOLD = 0.35

MODEL_DIR.mkdir(exist_ok=True)


PSYCHOLOGICAL_DISEASE_KEYWORDS = [
    "depression",
    "depressive",
    "anxiety",
    "panic",
    "bipolar",
    "schizophrenia",
    "psychosis",
    "psychotic",
    "ptsd",
    "post traumatic",
    "ocd",
    "obsessive",
    "compulsive",
    "phobia",
    "burnout",
    "insomnia",
    "sleep disorder",
    "eating disorder",
    "anorexia",
    "bulimia",
    "adhd",
    "autism",
    "personality disorder",
    "borderline",
    "dementia",
    "delirium",
    "substance abuse",
    "addiction",
    "alcohol abuse",
    "drug abuse",
    "suicidal",
    "mania",
    "manic",
    "mood disorder",
    "mental",
    "psychiatric",
]


EXCLUDED_DISEASE_KEYWORDS = [
    "acute respiratory distress syndrome",
    "ards",
    "birth trauma",
    "stress incontinence",
    "poisoning",
    "open wound",
    "fever",
    "infection",
    "diabetes",
    "ulcer",
    "chest",
    "knee",
    "cheek",
]


def find_dataset_path() -> Path:
    for path in DATASET_PATHS:
        if path.exists():
            return path

    raise FileNotFoundError(
        "Dataset not found. Put Final_Augmented_dataset_Diseases_and_Symptoms.csv "
        "in the same folder as train_model.py"
    )


def detect_target_column(df: pd.DataFrame) -> str:
    possible_targets = [
        "disease",
        "diseases",
        "Disease",
        "Diseases",
        "prognosis",
        "Prognosis",
        "diagnosis",
        "Diagnosis",
    ]

    for column in possible_targets:
        if column in df.columns:
            return column

    raise ValueError("Could not detect disease column.")


def is_psychological_disease(disease: str) -> bool:
    normalized = normalize_prompt_text(disease)

    if any(keyword in normalized for keyword in EXCLUDED_DISEASE_KEYWORDS):
        return False

    return any(keyword in normalized for keyword in PSYCHOLOGICAL_DISEASE_KEYWORDS)


def row_to_text(row: pd.Series, feature_columns: list[str]) -> str:
    symptoms: list[str] = []

    for column in feature_columns:
        value = row[column]

        try:
            numeric_value = float(value)
        except (TypeError, ValueError):
            numeric_value = 0.0

        if numeric_value > 0:
            symptoms.append(normalize_prompt_text(column))

    return normalize_prompt_text(" ".join(symptoms))


def create_text_model() -> Pipeline:
    return Pipeline(
        [
            (
                "tfidf",
                TfidfVectorizer(
                    lowercase=True,
                    ngram_range=(1, 2),
                    max_features=30000,
                    sublinear_tf=True,
                ),
            ),
            (
                "classifier",
                LogisticRegression(
                    max_iter=1500,
                    class_weight="balanced",
                    random_state=RANDOM_STATE,
                ),
            ),
        ]
    )


def add_natural_prompt_examples(
    psychological_df: pd.DataFrame,
    target_column: str,
) -> pd.DataFrame:
    """
    Adds natural human prompts so the model understands real user language.
    This keeps api.py minimal and lets the model learn common phrasing.
    """

    existing_diseases = set(
        psychological_df[target_column]
        .astype(str)
        .str.lower()
        .str.strip()
    )

    examples = [
        ("anxiety", "i feel very anxious nervous worried and afraid all the time"),
        ("anxiety", "i feel anxious and i cannot calm down"),
        ("anxiety", "i worry too much and feel nervous every day"),
        ("anxiety", "i feel very anxious and i have panic attacks every night"),
        ("panic attack", "i have panic attacks every night"),
        ("panic attack", "my heart races and i feel intense panic"),
        ("panic disorder", "i keep having panic attacks and fear another one will happen"),
        ("panic disorder", "i feel sudden fear panic and intense anxiety"),
        ("depression", "i feel sad hopeless tired and empty"),
        ("depression", "i lost interest in everything and nothing makes me happy"),
        ("depression", "i feel depressed worthless and i cry often"),
        ("depression", "i feel sad hopeless tired and i lost interest in everything"),
        ("postpartum depression", "after giving birth i feel depressed hopeless and tired"),
        ("primary insomnia", "i cannot sleep at night and i wake up tired"),
        ("primary insomnia", "i have insomnia and difficulty sleeping every night"),
        ("primary insomnia", "i cannot sleep i feel stressed and exhausted all day"),
        ("acute stress reaction", "i feel stressed overwhelmed shocked and unable to relax"),
        ("acute stress reaction", "after a traumatic event i feel stressed anxious and scared"),
        ("post-traumatic stress disorder (ptsd)", "i have flashbacks nightmares fear and trauma memories"),
        ("post-traumatic stress disorder (ptsd)", "i keep remembering the traumatic event and cannot feel safe"),
        ("post-traumatic stress disorder (ptsd)", "i have nightmares flashbacks and avoid reminders of what happened"),
        ("obsessive compulsive disorder (ocd)", "i have intrusive thoughts and compulsions"),
        ("obsessive compulsive disorder (ocd)", "i repeat actions many times because of obsessive thoughts"),
        ("obsessive compulsive disorder (ocd)", "i cannot stop repeated thoughts and repeated checking"),
        ("psychotic disorder", "i hear voices and see things that others do not see"),
        ("psychotic disorder", "i feel paranoid and believe people are watching me"),
        ("schizophrenia", "i hear voices feel paranoid and have strange beliefs"),
        ("schizophrenia", "i see things hear voices and feel disconnected from reality"),
        ("bipolar disorder", "my mood changes from very happy energetic to very depressed"),
        ("bipolar disorder", "i have manic episodes and then depression"),
        ("bipolar disorder", "sometimes i feel extremely energetic and then very sad"),
        ("attention deficit hyperactivity disorder (adhd)", "i cannot focus i am distracted and restless"),
        ("attention deficit hyperactivity disorder (adhd)", "i have attention problems and cannot concentrate"),
        ("attention deficit hyperactivity disorder (adhd)", "i am restless impulsive and cannot stay focused"),
        ("social phobia", "i feel intense fear when i meet people or speak in public"),
        ("social phobia", "i avoid social situations because i feel embarrassed and anxious"),
        ("eating disorder", "i am afraid of gaining weight and i avoid eating"),
        ("eating disorder", "i have unhealthy eating habits and worry too much about my body"),
        ("alcohol abuse", "i cannot stop drinking alcohol even when it harms me"),
        ("drug abuse", "i cannot stop using drugs and it affects my life"),
        ("substance-related mental disorder", "substance use is affecting my mood and behavior"),
    ]

    prompt_prefixes = [
        "",
        "i think ",
        "lately ",
        "for weeks ",
    ]
    prompt_suffixes = [
        "",
        " and it affects my daily life",
        " and i do not know what is happening",
        " and it is getting worse",
    ]

    rows = []

    for disease, text in examples:
        if disease.lower().strip() in existing_diseases:
            for prefix in prompt_prefixes:
                for suffix in prompt_suffixes:
                    augmented_text = normalize_prompt_text(f"{prefix}{text}{suffix}")
                    rows.append(
                        {
                            "text": augmented_text,
                            target_column: disease,
                            "scope": "psychological",
                        }
                    )

    if not rows:
        print("No natural prompt examples added. Disease names did not match dataset labels.")
        return psychological_df

    augmented_df = pd.DataFrame(rows).drop_duplicates(subset=["text", target_column, "scope"])
    print(f"Added {len(augmented_df)} natural psychological prompt examples.")

    return pd.concat([psychological_df, augmented_df], ignore_index=True)


def tune_scope_threshold(model: Pipeline, x_validation: pd.Series, y_validation: pd.Series) -> float:
    if not hasattr(model, "predict_proba"):
        return SCOPE_THRESHOLD

    classes = [str(label) for label in model.classes_]
    if "psychological" not in classes:
        return SCOPE_THRESHOLD

    positive_index = classes.index("psychological")
    probabilities = model.predict_proba(x_validation)[:, positive_index]
    expected = (y_validation == "psychological").astype(int)

    best_threshold = SCOPE_THRESHOLD
    best_score = -1.0

    for threshold in [step / 100 for step in range(10, 91, 5)]:
        predicted = (probabilities >= threshold).astype(int)
        score = fbeta_score(expected, predicted, beta=2, zero_division=0)

        if score > best_score:
            best_score = score
            best_threshold = threshold

    print(f"Tuned scope threshold: {best_threshold:.2f} (F2={best_score:.4f})")

    return best_threshold


def derive_disease_confidence_threshold(model: Pipeline, x_validation: pd.Series) -> float:
    if not hasattr(model, "predict_proba"):
        return DEFAULT_DISEASE_CONFIDENCE_THRESHOLD

    probabilities = model.predict_proba(x_validation)
    max_probabilities = probabilities.max(axis=1)
    threshold = float(max(0.25, min(0.60, max_probabilities.mean() - max_probabilities.std())))

    print(f"Derived disease confidence threshold: {threshold:.2f}")

    return threshold


def prepare_dataset() -> tuple[pd.DataFrame, pd.DataFrame, str]:
    print("Loading dataset...")

    dataset_path = find_dataset_path()
    print(f"Using dataset: {dataset_path}")

    df = pd.read_csv(dataset_path)
    df.columns = [str(column).strip() for column in df.columns]
    df = df.dropna(how="all").fillna(0)

    target_column = detect_target_column(df)

    print(f"Target column detected: {target_column}")
    print(f"Original dataset shape: {df.shape}")

    df[target_column] = df[target_column].astype(str)
    feature_columns = [column for column in df.columns if column != target_column]

    print("Converting symptom columns to text...")

    df = df.copy()
    df["text"] = df.apply(lambda row: row_to_text(row, feature_columns), axis=1)
    df["text"] = df["text"].apply(normalize_prompt_text)
    df = df[df["text"].str.strip() != ""].copy()

    df["scope"] = df[target_column].apply(
        lambda disease: "psychological" if is_psychological_disease(disease) else "non_psychological"
    )

    print("Scope distribution:")
    print(df["scope"].value_counts())

    psychological_df = df[df["scope"] == "psychological"].copy()
    if psychological_df.empty:
        raise ValueError("No psychological diseases found.")

    disease_counts = psychological_df[target_column].value_counts()
    valid_diseases = disease_counts[disease_counts >= MIN_SAMPLES_PER_DISEASE].index

    psychological_df = psychological_df[
        psychological_df[target_column].isin(valid_diseases)
    ].copy()

    psychological_df = add_natural_prompt_examples(psychological_df, target_column)

    scope_df = pd.concat(
        [
            df[["text", target_column, "scope"]],
            psychological_df[["text", target_column, "scope"]],
        ],
        ignore_index=True,
    ).drop_duplicates(subset=["text", target_column, "scope"])

    print(f"Psychological dataset shape: {psychological_df.shape}")
    print(f"Number of psychological diseases: {psychological_df[target_column].nunique()}")

    return scope_df, psychological_df, target_column


def train_and_evaluate_model(
    model_name: str,
    model: Pipeline,
    features: pd.Series,
    labels: pd.Series,
) -> tuple[Pipeline, pd.Series, pd.Series]:
    print(f"Training {model_name}...")

    x_train, x_test, y_train, y_test = train_test_split(
        features,
        labels,
        test_size=0.2,
        random_state=RANDOM_STATE,
        stratify=labels,
    )

    model.fit(x_train, y_train)
    predictions = model.predict(x_test)

    print(f"{model_name} accuracy:")
    print(accuracy_score(y_test, predictions))
    print(classification_report(y_test, predictions, zero_division=0))

    return model, x_test, y_test


def main() -> None:
    scope_df, psychological_df, target_column = prepare_dataset()

    scope_model, scope_x_test, scope_y_test = train_and_evaluate_model(
        "scope model",
        create_text_model(),
        scope_df["text"],
        scope_df["scope"],
    )

    disease_model, disease_x_test, _ = train_and_evaluate_model(
        "psychological disease model",
        create_text_model(),
        psychological_df["text"],
        psychological_df[target_column],
    )

    tuned_scope_threshold = tune_scope_threshold(scope_model, scope_x_test, scope_y_test)
    disease_confidence_threshold = derive_disease_confidence_threshold(disease_model, disease_x_test)

    bundle = {
        "scope_model": scope_model,
        "disease_model": disease_model,
        "scope_threshold": tuned_scope_threshold,
        "disease_confidence_threshold": disease_confidence_threshold,
        "target_column": target_column,
        "psychological_labels": sorted(psychological_df[target_column].astype(str).unique().tolist()),
        "training_size": {
            "scope_samples": int(len(scope_df)),
            "psychological_samples": int(len(psychological_df)),
        },
    }

    joblib.dump(bundle, MODEL_PATH)

    print("AI bundle saved successfully.")
    print(f"Created: {MODEL_PATH}")


if __name__ == "__main__":
    main()
