"""
predict.py
----------
مثال يوضح لفريق الباك إند كيف يستخدموا المودل المحفوظ لتوليد
Estimated Waiting Time لزبون جديد.

الملفات المطلوبة بنفس المجلد:
    waiting_time_model_v2.keras
    preprocessor_v2.pkl
    feature_order_v2.pkl
"""

import numpy as np
import tensorflow as tf
import joblib

# --- تحميل المودل والملفات المرافقة (مرة وحدة بس عند تشغيل السيرفر) ---
model = tf.keras.models.load_model("waiting_time_model_v2.keras")
preprocessor = joblib.load("preprocessor_v2.pkl")
feature_order = joblib.load("feature_order_v2.pkl")


def predict_waiting_time(customer_data: dict) -> float:
    """
    ترجع وقت الانتظار المتوقع بالدقائق لزبون جديد.

    customer_data لازم يحتوي المفاتيح التالية (بالضبط):
        queue_length_before   : عدد الأشخاص قبله بالطابور
        avg_service_time      : متوسط مدة الخدمة (دقيقة)
        available_staff       : عدد الموظفين المتاحين لنفس الخدمة الآن
        previous_waiting_time : وقت انتظار آخر Ticket "Done" لنفس service_id
                                 (= started_at - created_at لتلك التذكرة)
        hour                  : الساعة الحالية (0-23)
        day_of_week           : اليوم الحالي (0=اثنين ... 6=أحد)
        service_type          : واحدة من:
                                 "consultation", "checkup", "lab_test", "payment"
    """

    # 1. تحويل الساعة واليوم لنفس الترميز الدائري المستخدم أثناء التدريب
    hour = customer_data["hour"]
    dow = customer_data["day_of_week"]
    hour_sin = np.sin(2 * np.pi * hour / 24)
    hour_cos = np.cos(2 * np.pi * hour / 24)
    dow_sin = np.sin(2 * np.pi * dow / 7)
    dow_cos = np.cos(2 * np.pi * dow / 7)

    # 2. تحويل نوع الخدمة لنفس شكل الـ one-hot المستخدم أثناء التدريب
    service_type = customer_data["service_type"]
    service_flags = {
        "service_checkup": 1.0 if service_type == "checkup" else 0.0,
        "service_consultation": 1.0 if service_type == "consultation" else 0.0,
        "service_lab_test": 1.0 if service_type == "lab_test" else 0.0,
        "service_payment": 1.0 if service_type == "payment" else 0.0,
    }

    # 3. تجميع كل القيم بنفس الترتيب المحفوظ في feature_order (مهم جدًا!)
    row = {
        "queue_length_before": customer_data["queue_length_before"],
        "avg_service_time": customer_data["avg_service_time"],
        "available_staff": customer_data["available_staff"],
        "previous_waiting_time": customer_data["previous_waiting_time"],
        "hour_sin": hour_sin,
        "hour_cos": hour_cos,
        "dow_sin": dow_sin,
        "dow_cos": dow_cos,
        **service_flags,
    }
    x = np.array([[row[col] for col in feature_order]], dtype=float)

    # 4. تطبيق نفس الـ preprocessing المستخدم أثناء التدريب (إلزامي)
    x_processed = preprocessor.transform(x)

    # 5. التوقع
    prediction = model.predict(x_processed, verbose=0).flatten()[0]

    # الوقت لا يمكن أن يكون سالبًا
    return max(0.0, float(prediction))


# --- مثال استخدام ---
if __name__ == "__main__":
    example_customer = {
        "queue_length_before": 4,
        "avg_service_time": 10.5,
        "available_staff": 3,
        "previous_waiting_time": 12.0,
        "hour": 11,
        "day_of_week": 2,
        "service_type": "checkup",
    }

    result = predict_waiting_time(example_customer)
    print(f"Estimated Waiting Time: {result:.1f} دقيقة")
