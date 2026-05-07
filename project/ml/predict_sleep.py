import sys
import json
import joblib
import pandas as pd
from pathlib import Path

BASE_DIR = Path(__file__).resolve().parent
MODEL_PATH = BASE_DIR / "sleep_model.pkl"

if len(sys.argv) < 6:
    print(json.dumps({
        "success": False,
        "error": "Paramètres manquants"
    }))
    sys.exit(1)

duree_sommeil = float(sys.argv[1])
interruptions = int(sys.argv[2])
temperature = float(sys.argv[3])
bruit_niveau = int(sys.argv[4])
humeur_reveil = sys.argv[5]

bundle = joblib.load(MODEL_PATH)

model = bundle["model"]
humeur_encoder = bundle["humeur_encoder"]
qualite_encoder = bundle["qualite_encoder"]

if humeur_reveil not in list(humeur_encoder.classes_):
    humeur_reveil = "Neutre"

humeur_encoded = humeur_encoder.transform([humeur_reveil])[0]

input_data = pd.DataFrame([{
    "duree_sommeil": duree_sommeil,
    "interruptions": interruptions,
    "temperature": temperature,
    "bruit_niveau": bruit_niveau,
    "humeur_reveil_encoded": humeur_encoded,
}])

prediction = model.predict(input_data)[0]
qualite = qualite_encoder.inverse_transform([prediction])[0]

print(json.dumps({
    "success": True,
    "prediction": qualite,
    "accuracy": round(float(bundle["accuracy"]), 2)
}, ensure_ascii=False))