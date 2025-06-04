from flask import Flask, request, jsonify
import pandas as pd
import numpy as np
from sentence_transformers import SentenceTransformer
from sklearn.metrics.pairwise import cosine_similarity
import re

app = Flask(__name__)

# Load Data Once at Startup
courses_df = pd.read_csv(r"C:\xampp\htdocs\project\mentor_recommendation\courses_mentors.csv")
mentors_df = pd.read_csv(r"C:\xampp\htdocs\project\mentor_recommendation\updated_mentor_dataset_final_v2.csv")
mentors_df["Mentor_ID"] = mentors_df["Mentor_ID"].astype(str).str.strip()

# Load Model Once
model = SentenceTransformer("all-MiniLM-L6-v2")

# Compute course embeddings once at startup
course_embeddings = model.encode(courses_df["course_name"].astype(str).tolist(), convert_to_tensor=True)

# Preprocessing function
def preprocess_input(text):
    return re.sub(r"[^a-zA-Z0-9\s]", "", text.lower().strip())

# Define valid levels
def get_valid_levels(user_level):
    level_mapping = {
        "beginner": ["beginner", "intermediate"],
        "intermediate": ["intermediate", "advanced"],
        "advanced": ["advanced"]
    }
    return level_mapping.get(user_level.lower(), ["beginner", "intermediate", "advanced"])

# Find similar courses
def find_similar_courses(input_course, difficulty, top_n=3):
    user_input = preprocess_input(input_course)
    user_embedding = model.encode([user_input], convert_to_tensor=True)

    # Filter courses by difficulty
    valid_levels = get_valid_levels(difficulty)
    filtered_df = courses_df[courses_df["Difficulty_Level"].isin(valid_levels)]

    if filtered_df.empty:
        filtered_df = courses_df  # Use all courses if filtering removes all

    # Compute similarity
    similarity_scores = cosine_similarity(user_embedding.cpu().numpy(), course_embeddings[filtered_df.index].cpu().numpy()).flatten()
    
    if similarity_scores.size == 0:
        return pd.DataFrame()

    # Get top N similar courses
    top_indices = np.argsort(similarity_scores)[-top_n:][::-1]
    return filtered_df.iloc[top_indices][["course_name", "mentor_list"]]

# Get mentors for the selected courses
def get_mentors_for_courses(course_list):
    mentor_ids = set()
    for _, row in course_list.iterrows():
        mentors = row["mentor_list"]
        if pd.notna(mentors):
            mentor_ids.update(set(map(str.strip, mentors.split(";"))))
    return list(mentor_ids) if mentor_ids else []

# Filter mentors by rating
def filter_mentors_by_rating(mentor_ids, min_rating):
    eligible_mentors = mentors_df[
        (mentors_df["Mentor_ID"].isin(mentor_ids)) & (mentors_df["Average_Rating"] >= min_rating)
    ].copy()

    # Set a default image URL
    eligible_mentors["Image_URL"] = r"C:\xampp\htdocs\project\mentor_recommendation\mentor.png"

    return eligible_mentors[[
        "Mentor_ID", "Mentor_Name", "Average_Rating", "Expertise_Domains",
        "Total_Students_Mentored", "Years_of_Experience", "Preferred_Style_to_Teach",
        "Availability", "Mentorship Mode", "Languages Spoken", "Contact Information",
        "Image_URL"
    ]]

# API Endpoint for Mentor Recommendation
@app.route("/recommend_mentors", methods=["POST"])
def recommend_mentors():
    try:
        data = request.get_json()
        input_course = data.get("course", "").strip()
        difficulty = data.get("difficulty", "beginner").strip().lower()
        min_rating = float(data.get("min_rating", 4.0))

        if not input_course:
            return jsonify({"error": "Course name is required"}), 400

        # Find similar courses
        similar_courses = find_similar_courses(input_course, difficulty, top_n=3)

        if similar_courses.empty:
            return jsonify({"message": "No similar courses found"}), 200

        # Get mentors for the courses
        mentor_ids = get_mentors_for_courses(similar_courses)
        recommended_mentors = filter_mentors_by_rating(mentor_ids, min_rating)

        if recommended_mentors.empty:
            return jsonify({"message": "No mentors meet the rating criteria"}), 200

        return jsonify({"recommendations": recommended_mentors.to_dict(orient="records")})

    except Exception as e:
        print(f"Error: {str(e)}")
        return jsonify({"error": "Internal Server Error"}), 500

@app.route("/", methods=["GET"])
def home():
    return jsonify({"message": "Mentor Recommendation API is running!"})

if __name__ == "__main__":
    app.run(port=5001, debug=True, threaded=True)  # Multi-threaded for better performance
