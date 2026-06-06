import mysql.connector
from flask import Flask, render_template, request, redirect, url_for, send_from_directory
import cv2
import os
import numpy as np
from PIL import Image

app = Flask(__name__)

db_host = 'localhost'
db_user = 'root'
db_password = ''
db_database = 'hopespot'

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
image_folder_path = os.path.join(BASE_DIR, 'static', 'recognize')

# ✅ Static image serve route — BASE_DIR ke baad define kiya
@app.route('/recognize-img/<path:filename>')
def recognize_img(filename):
    return send_from_directory(image_folder_path, filename)

recognizer = cv2.face.LBPHFaceRecognizer_create()
face_cascade = cv2.CascadeClassifier(cv2.data.haarcascades + 'haarcascade_frontalface_default.xml')

known_face_names = []
trained = False

print(f"[INFO] Face images folder: {image_folder_path}")

def load_and_train():
    global trained, known_face_names
    faces = []
    labels = []
    known_face_names = []

    if not os.path.exists(image_folder_path):
        print(f"[WARNING] Recognize folder nahi mili: {image_folder_path}")
        return

    for idx, filename in enumerate(os.listdir(image_folder_path)):
        if filename.lower().endswith((".jpeg", ".png", ".jpg", ".webp")):
            image_path = os.path.join(image_folder_path, filename)
            try:
                pil_img = Image.open(image_path).convert('L')
                img_array = np.array(pil_img, dtype=np.uint8)
                detected = face_cascade.detectMultiScale(img_array, scaleFactor=1.1, minNeighbors=3, minSize=(20, 20))
                if len(detected) == 0:
                    print(f"[WARNING] {filename} mein face nahi mila, skip")
                    continue
                x, y, w, h = detected[0]
                face_roi = cv2.resize(img_array[y:y+h, x:x+w], (100, 100))
                faces.append(face_roi)
                labels.append(idx)
                known_face_names.append(os.path.splitext(filename)[0])
                print(f"[INFO] Loaded: {filename}")
            except Exception as e:
                print(f"[ERROR] {filename} load nahi hua: {e}")

    if len(faces) >= 1:
        recognizer.train(faces, np.array(labels))
        trained = True
        print(f"[INFO] Total {len(faces)} faces loaded aur trained.")
    else:
        print("[WARNING] Koi face nahi mila training ke liye.")

load_and_train()


def fetch_additional_info(matched_person_name):
    try:
        conn = mysql.connector.connect(
            host=db_host, user=db_user,
            password=db_password, database=db_database
        )
        cursor = conn.cursor()
        cursor.execute(
            "SELECT name, fname, age, phonenumber, moredetail FROM missing WHERE filename LIKE %s",
            (f"%{matched_person_name}%",)
        )
        row = cursor.fetchone()
        conn.close()
        return row if row else None
    except mysql.connector.Error as e:
        print("MySQL Error:", e)
        return None


def recognize_face_from_path(image_path):
    if not trained:
        return None, 0
    try:
        pil_img = Image.open(image_path).convert('L')
        img_array = np.array(pil_img, dtype=np.uint8)
        detected = face_cascade.detectMultiScale(img_array, scaleFactor=1.1, minNeighbors=3, minSize=(20, 20))
        if len(detected) == 0:
            print("[INFO] Image mein koi face detect nahi hua")
            return None, 0
        x, y, w, h = detected[0]
        face_roi = cv2.resize(img_array[y:y+h, x:x+w], (100, 100))
        label, confidence = recognizer.predict(face_roi)
        match_percentage = max(0, 100 - confidence)
        print(f"[INFO] Label: {label}, Confidence: {confidence:.2f}, Match%: {match_percentage:.2f}")
        if confidence < 80:
            return known_face_names[label], match_percentage
        else:
            return None, match_percentage
    except Exception as e:
        print(f"[ERROR] Recognition failed: {e}")
        return None, 0


@app.route('/')
def home():
    return render_template('foundperson.html')

@app.route('/foundperson')
def found_person():
    return render_template('foundperson.html')

@app.route('/recognize', methods=['POST'])
def recognize():
    if 'file' not in request.files:
        return render_template('foundperson.html', result="Koi image upload nahi ki gayi")
    uploaded_file = request.files['file']
    if uploaded_file.filename == '':
        return render_template('foundperson.html', result="File select nahi ki gayi")

    image_path = os.path.join(BASE_DIR, "temp.jpg")
    uploaded_file.save(image_path)

    try:
        matched_name, match_pct = recognize_face_from_path(image_path)
        if matched_name:
            # jpg ya jpeg dono check karo
            matched_image_name = None
            for ext in ['.jpg', '.jpeg', '.png', '.JPG', '.JPEG']:
                if os.path.exists(os.path.join(image_folder_path, matched_name + ext)):
                    matched_image_name = matched_name + ext
                    break
            if not matched_image_name:
                matched_image_name = matched_name + '.jpg'

            result = f"Match mila! {match_pct:.1f}% confidence"
            additional_info = fetch_additional_info(matched_name)
            if os.path.exists(image_path):
                os.remove(image_path)
            return render_template('persondetail.html',
                                   result=result,
                                   matched_image=matched_image_name,
                                   additional_info=additional_info)
        else:
            result = "Koi match nahi mila database mein"
    except Exception as e:
        print(f"[ERROR] {e}")
        result = f"Error: {e}"
    finally:
        if os.path.exists(image_path):
            os.remove(image_path)

    return render_template('foundperson.html', result=result, matched_image=None)


@app.route('/capture', methods=['POST'])
def capture():
    video_capture = cv2.VideoCapture(0)
    if not video_capture.isOpened():
        return render_template('foundperson.html', result="Camera nahi khul raha.")
    ret, frame = video_capture.read()
    video_capture.release()
    if not ret:
        return render_template('foundperson.html', result="Camera se frame nahi mila")

    image_path = os.path.join(BASE_DIR, "temp_capture.jpg")
    cv2.imwrite(image_path, frame)

    try:
        matched_name, match_pct = recognize_face_from_path(image_path)
        if matched_name:
            # jpg ya jpeg dono check karo
            matched_image_name = None
            for ext in ['.jpg', '.jpeg', '.png', '.JPG', '.JPEG']:
                if os.path.exists(os.path.join(image_folder_path, matched_name + ext)):
                    matched_image_name = matched_name + ext
                    break
            if not matched_image_name:
                matched_image_name = matched_name + '.jpg'

            result = f"Match mila! {match_pct:.1f}% confidence"
            additional_info = fetch_additional_info(matched_name)
            if os.path.exists(image_path):
                os.remove(image_path)
            return render_template('persondetail.html',
                                   result=result,
                                   matched_image=matched_image_name,
                                   additional_info=additional_info)
        else:
            result = "Koi match nahi mila database mein"
    except Exception as e:
        print(f"[ERROR] {e}")
        result = f"Error: {e}"
    finally:
        if os.path.exists(image_path):
            os.remove(image_path)

    return render_template('foundperson.html', result=result, matched_image=None)


if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5000)