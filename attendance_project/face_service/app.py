from flask import Flask, request, jsonify
import face_recognition
import mysql.connector
import os
import numpy as np
from datetime import datetime
import logging

# Configure logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
logger = logging.getLogger(__name__)

app = Flask(__name__)

# Database configuration
db_config = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'system'
}

# Load known faces from database
known_faces = {}  # {'student_id': face_encoding}

def load_known_faces():
    """Load student face encodings from database or image files"""
    try:
        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor(dictionary=True)
        
        # Get all students
        cursor.execute("SELECT id, name, student_id, profile_image FROM students WHERE profile_image IS NOT NULL")
        students = cursor.fetchall()
        
        base_path = os.path.abspath(os.path.join(os.path.dirname(__file__), '..', '..', '..'))
        
        for student in students:
            try:
                # Construct the full path to the image
                image_path = os.path.join(base_path, student['profile_image'])
                
                if os.path.exists(image_path):
                    # Load and encode the face
                    image = face_recognition.load_image_file(image_path)
                    face_encodings = face_recognition.face_encodings(image)
                    
                    if face_encodings:
                        # Store the encoding with the student ID
                        known_faces[student['id']] = {
                            'encoding': face_encodings[0],
                            'name': student['name'],
                            'student_id': student['student_id']
                        }
                        logger.info(f"Loaded face for student: {student['name']} (ID: {student['id']})")
                    else:
                        logger.warning(f"No face found in image for student: {student['name']} (ID: {student['id']})")
                else:
                    logger.warning(f"Image file not found: {image_path}")
            except Exception as e:
                logger.error(f"Error processing student {student['id']}: {str(e)}")
        
        logger.info(f"Loaded {len(known_faces)} student faces")
        cursor.close()
        conn.close()
    except Exception as e:
        logger.error(f"Database error: {str(e)}")

# Load faces when app starts
load_known_faces()

@app.route('/recognize', methods=['POST'])
def recognize():
    if 'image' not in request.files:
        return jsonify(success=False, message="No image sent")

    try:
        # Process the uploaded image
        image_file = request.files['image']
        image = face_recognition.load_image_file(image_file)
        face_locations = face_recognition.face_locations(image)
        face_encodings = face_recognition.face_encodings(image, face_locations)

        if not face_encodings:
            return jsonify(success=False, message="No face detected")

        # Get the first detected face
        face_encoding = face_encodings[0]
        
        # Compare with known faces
        best_match = None
        best_distance = 1.0  # Lower is better, 0 is perfect match
        
        for student_id, student_data in known_faces.items():
            # Calculate face distance (lower is better)
            face_distances = face_recognition.face_distance([student_data['encoding']], face_encoding)
            distance = face_distances[0]
            
            # Check if this is the best match so far
            if distance < best_distance and distance < 0.6:  # 0.6 threshold for good match
                best_distance = distance
                best_match = student_id
        
        # If we found a match
        if best_match:
            # Calculate confidence (convert distance to confidence score)
            confidence = 1.0 - best_distance
            
            # Mark attendance in database
            attendance_marked = mark_attendance(best_match)
            
            # Get student data
            student_data = known_faces[best_match]
            
            return jsonify(success=True, data={
                "id": best_match,
                "name": student_data['name'],
                "student_id": student_data['student_id'],
                "confidence": round(confidence, 2),
                "attendance_marked": attendance_marked
            })
        
        return jsonify(success=False, message="Face not recognized")
    
    except Exception as e:
        logger.error(f"Recognition error: {str(e)}")
        return jsonify(success=False, message=f"Error during recognition: {str(e)}")

def mark_attendance(student_id):
    """Mark attendance in the database for the recognized student"""
    try:
        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor()
        
        # Get student course ID
        cursor.execute("SELECT course_id FROM students WHERE id = %s", (student_id,))
        student = cursor.fetchone()
        
        if not student:
            logger.error(f"Student ID {student_id} not found in database")
            return False
            
        course_id = student[0]
        today = datetime.now().strftime('%Y-%m-%d')
        
        # Check if attendance already marked today
        cursor.execute("SELECT id FROM attendance WHERE student_id = %s AND date = %s", 
                      (student_id, today))
        existing = cursor.fetchone()
        
        if existing:
            # Update existing attendance to 'present'
            cursor.execute("UPDATE attendance SET status = 'present' WHERE student_id = %s AND date = %s",
                          (student_id, today))
            logger.info(f"Updated attendance for student ID {student_id}")
        else:
            # Insert new attendance record
            cursor.execute("INSERT INTO attendance (student_id, course_id, status, date, created_at) "
                          "VALUES (%s, %s, 'present', %s, NOW())",
                          (student_id, course_id, today))
            logger.info(f"Marked attendance for student ID {student_id}")
        
        conn.commit()
        cursor.close()
        conn.close()
        return True
        
    except Exception as e:
        logger.error(f"Error marking attendance: {str(e)}")
        return False

@app.route('/', methods=['GET'])
def index():
    """API status endpoint"""
    return jsonify({
        "status": "online",
        "service": "VisionNex Face Recognition API",
        "students_loaded": len(known_faces),
        "timestamp": datetime.now().isoformat()
    })

@app.route('/reload', methods=['GET'])
def reload_faces():
    """Reload student faces from database"""
    try:
        known_faces.clear()
        load_known_faces()
        return jsonify({
            "success": True,
            "message": f"Reloaded {len(known_faces)} student faces"
        })
    except Exception as e:
        return jsonify({
            "success": False,
            "message": f"Error reloading faces: {str(e)}"
        })

if __name__ == "__main__":
    # Load faces on startup
    load_known_faces()
    # Run the Flask app
    app.run(host='0.0.0.0', port=5000, debug=True)
