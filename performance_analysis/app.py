import numpy as np
import pandas as pd
from flask import Flask, request, jsonify
from flask_cors import CORS
from sklearn.ensemble import RandomForestClassifier
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import StandardScaler, LabelEncoder
from imblearn.over_sampling import SMOTE

# Initialize Flask app
app = Flask(__name__)
CORS(app)  # Enable CORS for PHP frontend

# Load dataset
DATA_PATH = r"C:\xampp\htdocs\project\performance_analysis\restored_student_performance_dataset.csv"

try:
    df = pd.read_csv(DATA_PATH)
    print("✅ Dataset Loaded Successfully!")
except Exception as e:
    print(f"❌ Error loading dataset: {e}")
    df = None

# Train the model if dataset is loaded successfully
if df is not None:
    try:
        # Encode the Final_Label column (Target variable)
        label_encoder = LabelEncoder()
        df["Final_Label_Encoded"] = label_encoder.fit_transform(df["Final_Label"])

        # Define label mappings
        label_mapping = dict(zip(label_encoder.classes_, label_encoder.transform(label_encoder.classes_)))
        print(f"🎯 Label Mapping: {label_mapping}")

        # Prepare Features and Target
        X = df.drop(columns=["Final_Label", "Final_Label_Encoded"])  # Drop original label
        y = df["Final_Label_Encoded"]

        # Split dataset into train-test sets
        X_train, X_test, y_train, y_test = train_test_split(
            X, y, test_size=0.2, random_state=42, stratify=y
        )

        # Check original class distribution
        print("\n📊 Class Distribution Before SMOTE:")
        print(y_train.value_counts())

        # Apply SMOTE for class balancing
        smote = SMOTE(sampling_strategy={1: 100, 2: 150}, random_state=42)
        X_train_resampled, y_train_resampled = smote.fit_resample(X_train, y_train)

        # Check new class distribution
        print("\n📊 Class Distribution After SMOTE:")
        print(pd.Series(y_train_resampled).value_counts())

        # Apply Standardization
        scaler = StandardScaler()
        X_train_scaled = scaler.fit_transform(X_train_resampled)
        X_test_scaled = scaler.transform(X_test)

        # Train RandomForest Model
        model = RandomForestClassifier(
            n_estimators=150, max_depth=None, min_samples_split=4, min_samples_leaf=2, random_state=42
        )
        model.fit(X_train_scaled, y_train_resampled)

        print("✅ Model trained successfully!")

    except Exception as e:
        print(f"❌ Error training model: {e}")
        model = None

else:
    model = None

# Home Route
@app.route("/", methods=["GET"])
def home():
    return jsonify({"message": "Student Performance Prediction API is running!"})

# Prediction Endpoint
@app.route("/predict", methods=["POST"])
def predict():
    try:
        if model is None:
            return jsonify({"error": "Model not loaded. Check dataset and training."}), 500

        # Get JSON request data
        data = request.get_json()
        print("🔹 Received Input Data:", data)

        # Convert data to DataFrame
        input_data = pd.DataFrame([data])

        # Ensure input matches trained model feature names
        expected_features = list(X.columns)
        missing_features = set(expected_features) - set(input_data.columns)

        if missing_features:
            return jsonify({"error": f"Missing required features: {missing_features}"}), 400

        # Reorder input columns to match training data
        input_data = input_data[expected_features]

        # Apply Standardization
        input_data_scaled = scaler.transform(input_data)

        # Make prediction
        prediction = model.predict(input_data_scaled)[0]

        # Convert numeric prediction to label
        predicted_label = {v: k for k, v in label_mapping.items()}.get(prediction, "Unknown")

        print(f"✅ Predicted Label: {predicted_label}")

        return jsonify({"prediction": predicted_label})

    except Exception as e:
        print(f"❌ Error in Prediction: {str(e)}")
        return jsonify({"error": str(e)}), 500

# Run Flask server on port 5003
if __name__ == "__main__":
    app.run(debug=True, port=5003)
