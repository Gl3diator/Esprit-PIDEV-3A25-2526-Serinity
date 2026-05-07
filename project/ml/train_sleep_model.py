import pandas as pd
import joblib
from pathlib import Path

from sklearn.tree import DecisionTreeClassifier
from sklearn.preprocessing import LabelEncoder
from sklearn.model_selection import train_test_split
from sklearn.metrics import accuracy_score

BASE_DIR = Path(__file__).resolve().parent
DATA_PATH = BASE_DIR / "sleep_data.csv"
MODEL_PATH = BASE_DIR / "sleep_model.pkl"

data = pd.read_csv(DATA_PATH)

humeur_encoder = LabelEncoder()
qualite_encoder = LabelEncoder()

data["humeur_reveil_encoded"] = humeur_encoder.fit_transform(data["humeur_reveil"])
data["qualite_encoded"] = qualite_encoder.fit_transform(data["qualite"])

X = data[
    [
        "duree_sommeil",
        "interruptions",
        "temperature",
        "bruit_niveau",
        "humeur_reveil_encoded",
    ]
]

y = data["qualite_encoded"]

X_train, X_test, y_train, y_test = train_test_split(
    X,
    y,
    test_size=0.2,
    random_state=42,
    stratify=y
)

model = DecisionTreeClassifier(random_state=42)
model.fit(X_train, y_train)

predictions = model.predict(X_test)
accuracy = accuracy_score(y_test, predictions)

joblib.dump(
    {
        "model": model,
        "humeur_encoder": humeur_encoder,
        "qualite_encoder": qualite_encoder,
        "accuracy": accuracy,
    },
    MODEL_PATH
)

print("Modèle entraîné avec succès")
print(f"Accuracy: {accuracy:.2f}")
print(f"Modèle sauvegardé dans: {MODEL_PATH}")