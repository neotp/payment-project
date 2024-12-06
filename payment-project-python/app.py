from flask import Flask, render_template, request, redirect, url_for, flash, session
# from sapService import get_system_status

app = Flask(__name__)
app.secret_key = "your_secret_key"  # Replace with a strong key

# Routes
@app.route("/")
def home():
    return render_template("component/login/login.html")

@app.route("/login", methods=["POST"])
def login():
    username = request.form.get("username")
    password = request.form.get("password")
    
    if not username or not password:
        flash("Username and password are required", "warning")
        return redirect(url_for("home"))
    
    if username == "admin" and password == "1234":
        flash("Login successful", "success")
        return redirect(url_for("payment"))
    else:
        flash("Invalid credentials, please try again", "danger")
        return redirect(url_for("home"))

@app.route("/register", methods=["GET", "POST"])
def register():
    return render_template("component/register/register.html")

@app.route("/frgpss", methods=["GET", "POST"])
def frgpss():
    return render_template("component/frgpss/frgpss.html")

@app.route("/dashboard")
def dashboard():
    return "Welcome to the dashboard!"

# @app.route("/sap_status", methods=["GET"])
# def sap_status():
#     try:
#         status = get_system_status()
#         return jsonify(status)
#     except Exception as e:
#         return jsonify({"error": str(e)}), 500

if __name__ == "__main__":
    app.run(debug=True)
