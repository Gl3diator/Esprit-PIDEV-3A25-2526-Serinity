import os
import joblib
import pandas as pd

from sklearn.pipeline import Pipeline
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.linear_model import LogisticRegression
from sklearn.model_selection import train_test_split
from sklearn.metrics import accuracy_score, classification_report


DATASET_PATHS = [
    "Final_Augmented_dataset_Diseases_and_Symptoms.csv",
    "Final_Augmented_dataset_Diseases_and_Symptoms",
]

MODEL_DIR = "model"
MODEL_PATH = os.path.join(MODEL_DIR, "psychological_ai_bundle.pkl")

os.makedirs(MODEL_DIR, exist_ok=True)


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


def find_dataset_path() -> str:
    for path in DATASET_PATHS:
        if os.path.exists(path):
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


def normalize_text(value: str) -> str:
    return str(value).lower().replace("_", " ").strip()


def is_psychological_disease(disease: str) -> bool:
    normalized = normalize_text(disease)

    if any(keyword in normalized for keyword in EXCLUDED_DISEASE_KEYWORDS):
        return False

    return any(keyword in normalized for keyword in PSYCHOLOGICAL_DISEASE_KEYWORDS)


def row_to_text(row: pd.Series, feature_columns: list[str]) -> str:
    symptoms = []

    for column in feature_columns:
        value = row[column]

        try:
            numeric_value = float(value)
        except (TypeError, ValueError):
            numeric_value = 0.0

        if numeric_value > 0:
            symptoms.append(column.replace("_", " "))

    return " ".join(symptoms)


def create_text_model() -> Pipeline:
    return Pipeline([
        (
            "tfidf",
            TfidfVectorizer(
                lowercase=True,
                ngram_range=(1, 2),
                max_features=30000
            )
        ),
        (
            "classifier",
            LogisticRegression(
                max_iter=1200,
                class_weight="balanced"
            )
        )
    ])


def add_natural_prompt_examples(
    psychological_df: pd.DataFrame,
    target_column: str
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
        # Anxiety / panic
        ("anxiety", "i feel very anxious nervous worried and afraid all the time"),
        ("anxiety", "i feel anxious and i cannot calm down"),
        ("anxiety", "i worry too much and feel nervous every day"),
        ("anxiety", "i feel very anxious and i have panic attacks every night"),
        ("panic attack", "i have panic attacks every night"),
        ("panic attack", "my heart races and i feel intense panic"),
        ("panic disorder", "i keep having panic attacks and fear another one will happen"),
        ("panic disorder", "i feel sudden fear panic and intense anxiety"),

        # Depression
        ("depression", "i feel sad hopeless tired and empty"),
        ("depression", "i lost interest in everything and nothing makes me happy"),
        ("depression", "i feel depressed worthless and i cry often"),
        ("depression", "i feel sad hopeless tired and i lost interest in everything"),
        ("postpartum depression", "after giving birth i feel depressed hopeless and tired"),

        # Insomnia / stress
        ("primary insomnia", "i cannot sleep at night and i wake up tired"),
        ("primary insomnia", "i have insomnia and difficulty sleeping every night"),
        ("primary insomnia", "i cannot sleep i feel stressed and exhausted all day"),
        ("acute stress reaction", "i feel stressed overwhelmed shocked and unable to relax"),
        ("acute stress reaction", "after a traumatic event i feel stressed anxious and scared"),

        # PTSD
        ("post-traumatic stress disorder (ptsd)", "i have flashbacks nightmares fear and trauma memories"),
        ("post-traumatic stress disorder (ptsd)", "i keep remembering the traumatic event and cannot feel safe"),
        ("post-traumatic stress disorder (ptsd)", "i have nightmares flashbacks and avoid reminders of what happened"),

        # OCD
        ("obsessive compulsive disorder (ocd)", "i have intrusive thoughts and compulsions"),
        ("obsessive compulsive disorder (ocd)", "i repeat actions many times because of obsessive thoughts"),
        ("obsessive compulsive disorder (ocd)", "i cannot stop repeated thoughts and repeated checking"),

        # Psychosis / schizophrenia
        ("psychotic disorder", "i hear voices and see things that others do not see"),
        ("psychotic disorder", "i feel paranoid and believe people are watching me"),
        ("schizophrenia", "i hear voices feel paranoid and have strange beliefs"),
        ("schizophrenia", "i see things hear voices and feel disconnected from reality"),

        # Bipolar
        ("bipolar disorder", "my mood changes from very happy energetic to very depressed"),
        ("bipolar disorder", "i have manic episodes and then depression"),
        ("bipolar disorder", "sometimes i feel extremely energetic and then very sad"),

        # ADHD
        ("attention deficit hyperactivity disorder (adhd)", "i cannot focus i am distracted and restless"),
        ("attention deficit hyperactivity disorder (adhd)", "i have attention problems and cannot concentrate"),
        ("attention deficit hyperactivity disorder (adhd)", "i am restless impulsive and cannot stay focused"),

        # Social phobia
        ("social phobia", "i feel intense fear when i meet people or speak in public"),
        ("social phobia", "i avoid social situations because i feel embarrassed and anxious"),

        # Eating disorder
        ("eating disorder", "i am afraid of gaining weight and i avoid eating"),
        ("eating disorder", "i have unhealthy eating habits and worry too much about my body"),

        # Substance / addiction
        ("alcohol abuse", "i cannot stop drinking alcohol even when it harms me"),
        ("drug abuse", "i cannot stop using drugs and it affects my life"),
        ("substance-related mental disorder", "substance use is affecting my mood and behavior"),
    ]

    rows = []

    for disease, text in examples:
        if disease.lower().strip() in existing_diseases:
            rows.append({
                "text": text,
                target_column: disease,
                "scope": "psychological",
            })

    if not rows:
        print("No natural prompt examples added. Disease names did not match dataset labels.")
        return psychological_df

    augmented_df = pd.DataFrame(rows)

    print(f"Added {len(augmented_df)} natural psychological prompt examples.")

    return pd.concat([psychological_df, augmented_df], ignore_index=True)


def main() -> None:
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

    feature_columns = [
        column for column in df.columns
        if column != target_column
    ]

    print("Converting symptom columns to text...")

    df = df.copy()

    df["text"] = df.apply(
        lambda row: row_to_text(row, feature_columns),
        axis=1
    )

    df = df[df["text"].str.strip() != ""].copy()

    df["scope"] = df[target_column].apply(
        lambda disease: "psychological"
        if is_psychological_disease(disease)
        else "non_psychological"
    )

    print("Scope distribution:")
    print(df["scope"].value_counts())

    psychological_df = df[df["scope"] == "psychological"].copy()

    if psychological_df.empty:
        raise ValueError("No psychological diseases found.")

    disease_counts = psychological_df[target_column].value_counts()
    valid_diseases = disease_counts[disease_counts >= 2].index

    psychological_df = psychological_df[
        psychological_df[target_column].isin(valid_diseases)
    ].copy()

    psychological_df = add_natural_prompt_examples(
        psychological_df,
        target_column
    )

    natural_scope_examples = psychological_df[["text", target_column, "scope"]].tail(80)

    df = pd.concat(
        [
            df[["text", target_column, "scope"]],
            natural_scope_examples,
        ],
        ignore_index=True
    )

    print(f"Psychological dataset shape: {psychological_df.shape}")
    print(f"Number of psychological diseases: {psychological_df[target_column].nunique()}")

    print("Training scope model...")

    scope_x_train, scope_x_test, scope_y_train, scope_y_test = train_test_split(
        df["text"],
        df["scope"],
        test_size=0.2,
        random_state=42,
        stratify=df["scope"]
    )

    scope_model = create_text_model()
    scope_model.fit(scope_x_train, scope_y_train)

    scope_predictions = scope_model.predict(scope_x_test)

    print("Scope model accuracy:")
    print(accuracy_score(scope_y_test, scope_predictions))
    print(classification_report(scope_y_test, scope_predictions, zero_division=0))

    print("Training psychological disease model...")

    disease_x_train, disease_x_test, disease_y_train, disease_y_test = train_test_split(
        psychological_df["text"],
        psychological_df[target_column],
        test_size=0.2,
        random_state=42,
        stratify=psychological_df[target_column]
    )

    disease_model = create_text_model()
    disease_model.fit(disease_x_train, disease_y_train)

    disease_predictions = disease_model.predict(disease_x_test)

    print("Disease model accuracy:")
    print(accuracy_score(disease_y_test, disease_predictions))
    print(classification_report(disease_y_test, disease_predictions, zero_division=0))

    bundle = {
        "scope_model": scope_model,
        "disease_model": disease_model,
        "scope_threshold": 0.20,
    }

    joblib.dump(bundle, MODEL_PATH)

    print("AI bundle saved successfully.")
    print(f"Created: {MODEL_PATH}")


if __name__ == "__main__":
    main()