from flask import Flask, request, jsonify
from flask_cors import CORS
import pandas as pd
import numpy as np
import re
import torch  # Added to handle tensor operations
from sentence_transformers import SentenceTransformer
from sklearn.metrics.pairwise import cosine_similarity

# Load dataset with proper encoding and column correction
df = pd.read_csv(r"C:\xampp\htdocs\project\course_recommendation_system\backend\Manual_data_updated.csv", encoding="latin1")

# Rename misencoded column
df.rename(columns={"ï»¿Course_Name": "Course_Name"}, inplace=True)

# Normalize Difficulty_Level values
df["Difficulty_Level"] = df["Difficulty_Level"].str.lower().fillna("unknown")

# Combine relevant columns with refined weights
df["Combined_Text"] = (
    (df["Course_Name"].fillna("") + " ") * 3 +
    (df["Description"].fillna("") + " ") * 1 +
    (df["Skills_Covered"].fillna("") + " ") * 5 +
    (df["Tags"].fillna("") + " ") * 4
)

# Load sentence transformer model
model = SentenceTransformer("all-MiniLM-L6-v2")

# Convert course descriptions into numerical vectors
course_embeddings = model.encode(df["Combined_Text"].tolist(), convert_to_tensor=True)

# Initialize Flask API
app = Flask(__name__)  # Fixed __name__
CORS(app)

# Function to clean user input
def preprocess_input(text):
    text = text.lower().strip()
    text = re.sub(r"[^a-zA-Z0-9\s,]", "", text)  # Allow commas for multi-word inputs
    return text.strip()

# Function to determine eligible difficulty levels
def get_valid_levels(user_level):
    level_mapping = {
        "beginner": ["beginner"],
        "intermediate": ["intermediate"],
        "advanced": ["advanced"]
    }
    return level_mapping.get(user_level.lower(), ["beginner", "intermediate", "advanced"])  # Default to all

# Function to recommend courses
def recommend_courses(user_query, user_level="beginner", top_n=5):
    user_query = preprocess_input(user_query)
    
    if not user_query:
        return []  # Return empty list if no input
    
    user_embedding = model.encode([user_query], convert_to_tensor=True)

    # Filter dataset based on valid difficulty levels
    valid_levels = get_valid_levels(user_level)
    filtered_df = df[df["Difficulty_Level"].isin(valid_levels)]

    if filtered_df.empty:
        filtered_df = df  # Fallback to all courses if no match found

    # Compute similarity scores
    similarity_scores = cosine_similarity(user_embedding.cpu(), course_embeddings[filtered_df.index].cpu()).flatten()

    # Set threshold based on median similarity
    threshold = np.median(similarity_scores)
    filtered_indices = np.where(similarity_scores > threshold)[0]

    # Get top N recommendations
    if filtered_indices.size > 0:
        recommended_indices = np.argsort(similarity_scores[filtered_indices])[-top_n:][::-1]
        recommendations = filtered_df.iloc[filtered_indices[recommended_indices]][["Course_Name", "Difficulty_Level", "Description"]]
    else:
        recommendations = filtered_df.iloc[np.argsort(similarity_scores)[-top_n:]][["Course_Name", "Difficulty_Level", "Description"]]

    return recommendations.to_dict(orient="records")

# Home route to confirm API is running
@app.route("/", methods=["GET"])
def home():
    return jsonify({"message": "Course Recommendation API is running!"})

# API route to get recommendations
@app.route("/recommend", methods=["POST"])
def get_recommendations():
    data = request.json
    user_query = data.get("query", "").strip()
    user_level = data.get("level", "beginner").strip().lower()

    if not user_query:
        return jsonify({"error": "Query cannot be empty!"}), 400

    recommendations = recommend_courses(user_query, user_level)
    return jsonify({"recommendations": recommendations})

# Run the API
if __name__ == "__main__":  # Fixed __name__
    app.run(debug=True, port=5000)
