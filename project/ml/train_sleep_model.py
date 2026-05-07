import pandas as pd
import joblib
from pathlib import Path

from sklearn.compose import ColumnTransformer
from sklearn.pipeline import Pipeline
from sklearn.preprocessing import OneHotEncoder, LabelEncoder
from sklearn.tree import DecisionTreeClassifier
from sklearn.model_selection import train_test_split
from sklearn.metrics import accuracy_score

BASE_DIR = Path(__file__).resolve().parent
DATA_PATH = BASE_DIR / "sleep_data.csv"
MODEL_PATH = BASE_DIR / "sleep_model.pkl"

data = pd.read_csv(DATA_PATH)

X = data[
    [
        "duree_sommeil",
        "interruptions",
        "temperature",
        "bruit_niveau",
        "humeur_reveil",
    ]
]

y_raw = data["qualite"]

target_encoder = LabelEncoder()
y = target_encoder.fit_transform(y_raw)

numeric_features = [
    "duree_sommeil",
    "interruptions",
    "temperature",
    "bruit_niveau",
]

categorical_features = ["humeur_reveil"]

preprocessor = ColumnTransformer(
    transformers=[
        ("num", "passthrough", numeric_features),
        ("cat", OneHotEncoder(handle_unknown="ignore"), categorical_features),
    ]
)

pipeline = Pipeline(
    steps=[
        ("preprocessor", preprocessor),
        ("classifier", DecisionTreeClassifier(random_state=42)),
    ]
)

X_train, X_test, y_train, y_test = train_test_split(
    X,
    y,
    test_size=0.2,
    random_state=42,
    stratify=y
)

pipeline.fit(X_train, y_train)

predictions = pipeline.predict(X_test)
accuracy = accuracy_score(y_test, predictions)

joblib.dump(
    {
        "pipeline": pipeline,
        "target_encoder": target_encoder,
        "accuracy": accuracy,
    },
    MODEL_PATH
)

print("Modèle entraîné avec succès")
print(f"Accuracy: {accuracy:.2f}")
print(f"Modèle sauvegardé dans : {MODEL_PATH}")