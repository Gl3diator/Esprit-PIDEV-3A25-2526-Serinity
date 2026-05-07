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
    }, ensure_ascii=False))
    sys.exit(1)

try:
    duree_sommeil = float(sys.argv[1])
    interruptions = int(sys.argv[2])
    temperature = float(sys.argv[3])
    bruit_niveau = int(sys.argv[4])
    humeur_reveil = sys.argv[5]

    bundle = joblib.load(MODEL_PATH)

    pipeline = bundle["pipeline"]
    target_encoder = bundle["target_encoder"]

    input_data = pd.DataFrame([{
        "duree_sommeil": duree_sommeil,
        "interruptions": interruptions,
        "temperature": temperature,
        "bruit_niveau": bruit_niveau,
        "humeur_reveil": humeur_reveil,
    }])

    prediction = pipeline.predict(input_data)[0]
    qualite = target_encoder.inverse_transform([prediction])[0]

    print(json.dumps({
        "success": True,
        "prediction": qualite,
        "accuracy": round(float(bundle["accuracy"]), 2)
    }, ensure_ascii=False))

except Exception as e:
    print(json.dumps({
        "success": False,
        "error": str(e)
    }, ensure_ascii=False))
    sys.exit(1)