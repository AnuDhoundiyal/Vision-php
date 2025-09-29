import face_recognition
import sys, os, json

# Input image path
input_path = sys.argv[1]

# Folders
students_folder = "../../uploads/students"
teachers_folder = "../../dashboard/admin/uploads/teachers"

# Load known faces
known_encodings = []
known_ids = []

def load_faces(folder):
    for file in os.listdir(folder):
        if file.endswith(".jpg") or file.endswith(".png"):
            img_path = os.path.join(folder, file)
            image = face_recognition.load_image_file(img_path)
            encodings = face_recognition.face_encodings(image)
            if encodings:
                known_encodings.append(encodings[0])
                known_ids.append(file.split('.')[0])  # filename = student_id or teacher_id

load_faces(students_folder)
load_faces(teachers_folder)

# Load input image
unknown_image = face_recognition.load_image_file(input_path)
unknown_encodings = face_recognition.face_encodings(unknown_image)

if not unknown_encodings:
    print(json.dumps({'success': False, 'message': 'No face found'}))
    sys.exit()

unknown_encoding = unknown_encodings[0]

# Compare
results = face_recognition.compare_faces(known_encodings, unknown_encoding, tolerance=0.4)
distances = face_recognition.face_distance(known_encodings, unknown_encoding)

if True in results:
    index = results.index(True)
    confidence = 1 - distances[index]
    matched_id = known_ids[index]
    print(json.dumps({'success': True, 'data': {'student_id': matched_id, 'name': matched_id, 'confidence': confidence}}))
else:
    print(json.dumps({'success': False, 'message': 'Face not recognized'}))
