import os
import matplotlib.pyplot as plt
import matplotlib.patches as patches

# Konfigurasi Direktori Output
BASE_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
OUTPUT_DIR = os.path.join(BASE_DIR, "docs", "diagrams")
os.makedirs(OUTPUT_DIR, exist_ok=True)

# Set global matplotlib parameters for clean layout
plt.rcParams['font.sans-serif'] = 'DejaVu Sans'
plt.rcParams['font.family'] = 'sans-serif'

def draw_box(ax, text, x, y, w, h, facecolor, edgecolor, textcolor='black', fontsize=9, fontweight='normal', align='center'):
    """Helper to draw a colored box with text inside."""
    rect = patches.FancyBboxPatch(
        (x, y), w, h,
        boxstyle="round,pad=0.03",
        facecolor=facecolor,
        edgecolor=edgecolor,
        linewidth=1.5,
        zorder=2
    )
    ax.add_patch(rect)
    
    # Calculate text alignment
    if align == 'center':
        tx, ty = x + w/2, y + h/2
        ax.text(tx, ty, text, color=textcolor, fontsize=fontsize, fontweight=fontweight,
                ha='center', va='center', wrap=True, zorder=3)
    elif align == 'left':
        tx, ty = x + 0.1, y + h/2
        ax.text(tx, ty, text, color=textcolor, fontsize=fontsize, fontweight=fontweight,
                ha='left', va='center', wrap=True, zorder=3)

def draw_arrow(ax, sx, sy, ex, ey, text="", text_pos=None, arrowstyle="->", connectionstyle=None, color='#555555', linewidth=1.5):
    """Helper to draw a clean arrow between components."""
    # Custom arrow styles
    arrowprops = dict(
        arrowstyle=arrowstyle,
        color=color,
        linewidth=linewidth,
        zorder=1
    )
    if connectionstyle:
        arrowprops['connectionstyle'] = connectionstyle
        
    ax.annotate("", xy=(ex, ey), xytext=(sx, sy), arrowprops=arrowprops)
    
    if text and text_pos:
        ax.text(text_pos[0], text_pos[1], text, color='#333333', fontsize=7.5,
                ha='center', va='center', backgroundcolor='white', wrap=True, zorder=4)

# ==============================================================================
# DIAGRAM 1: ARSITEKTUR SISTEM KESELURUHAN
# ==============================================================================
def generate_diagram_1():
    fig, ax = plt.subplots(figsize=(12, 8))
    ax.set_xlim(0, 11)
    ax.set_ylim(0, 10)
    ax.axis('off')
    
    # Title
    ax.text(5.5, 9.6, "DIAGRAM 1: ARSITEKTUR SISTEM KESELURUHAN (DEL CARE / EMOLENS)", 
            fontsize=14, fontweight='bold', ha='center')
    
    # --- CLIENT TIER (TEMA EMOLENS: Green #9BAA7F & Soft Pink) ---
    draw_box(ax, "MAHASISWA (Client)\nFlutter Mobile App\n(EMOLENS)", 1.0, 7.8, 2.8, 1.2, 
             facecolor='#9BAA7F', edgecolor='#7E8E64', textcolor='white', fontweight='bold', fontsize=10)
    
    draw_box(ax, "KONSELOR (Admin)\nWeb Dashboard\n(Del Care Web)", 5.2, 7.8, 2.8, 1.2, 
             facecolor='#3F51B5', edgecolor='#303F9F', textcolor='white', fontweight='bold', fontsize=10)
             
    # --- BACKEND TIER ---
    draw_box(ax, "LARAVEL API & WEB BACKEND GATEWAY\n\n- API Provider for Mobile Client\n- Web Views & Admin Dashboard Controller\n- Third-Party Clients (Cloudinary, CIS, FastAPI)", 
             2.8, 4.8, 4.4, 1.8, facecolor='#009688', edgecolor='#00796B', textcolor='white', fontweight='bold', fontsize=10)

    # --- DATABASE TIER (DUAL DB) ---
    draw_box(ax, "MONGODB ATLAS (NoSQL)\n\n- daily_checkins (mood & feeling)\n- journalTexts (deskripsi jurnal)\n- stories (mood sharing)\n- modules (edukasi)", 
             0.1, 1.2, 3.2, 2.0, facecolor='#E8F5E9', edgecolor='#4CAF50', textcolor='#1B5E20', fontsize=9)
             
    draw_box(ax, "MYSQL DATABASE (RDBMS)\n\n- users & counselors credentials\n- jadwal_konseling (appointments)\n- laporan_konseling (counseling notes)\n- notifications (MySQL queue)", 
             0.1, 3.4, 3.2, 1.1, facecolor='#E3F2FD', edgecolor='#2196F3', textcolor='#0D47A1', fontsize=9)

    # --- AI / ML TIER ---
    draw_box(ax, "FASTAPI AI ENGINE (Python)\n\n- IndoBERT NLP Severity Classifier\n- Rule-Based Red Flag Pattern Scanner\n- Clinical Predictive Risk Evaluator\n- Groq LLM & Report Summarizer", 
             4.3, 1.2, 3.5, 2.0, facecolor='#FFF3E0', edgecolor='#FF9800', textcolor='#E65100', fontsize=9)

    # --- EXTERNAL SERVICES TIER ---
    draw_box(ax, "API CIS KAMPUS DEL\n(Autentikasi & Sinkronisasi Mahasiswa)", 8.4, 6.8, 2.2, 0.8, 
             facecolor='#FFEBEE', edgecolor='#EF5350', textcolor='#C62828', fontsize=8.5)
             
    draw_box(ax, "CLOUDINARY API\n(Penyimpanan PDF & Gambar Modul)", 8.4, 5.0, 2.2, 0.8, 
             facecolor='#FFEBEE', edgecolor='#EF5350', textcolor='#C62828', fontsize=8.5)
             
    draw_box(ax, "GROQ CLOUD API\n(Model Llama 3.3 70B\nPopup & Rekomendasi Quote)", 8.4, 2.0, 2.2, 1.0, 
             facecolor='#FFEBEE', edgecolor='#EF5350', textcolor='#C62828', fontsize=8.5)

    # --- ARROWS & INTERACTIONS ---
    # 1. Client to Backend
    draw_arrow(ax, 2.4, 7.8, 3.5, 6.6, "REST API\nHTTP/JSON", (2.7, 7.2), arrowstyle="<->")
    draw_arrow(ax, 6.6, 7.8, 6.0, 6.6, "Web Requests\nHTTP/HTML", (6.5, 7.2), arrowstyle="<->")
    
    # 2. Backend to Databases
    draw_arrow(ax, 2.8, 4.8, 1.5, 4.5, "MySQL Connection", (2.0, 4.7), arrowstyle="->", connectionstyle="arc3,rad=-0.1")
    draw_arrow(ax, 3.5, 4.8, 3.3, 2.8, "MongoDB Client\n(laravel-mongodb)", (3.6, 3.8), arrowstyle="->", connectionstyle="angle,angleA=-90,angleB=180,rad=5")

    # 3. Backend to FastAPI AI Engine
    draw_arrow(ax, 5.0, 4.8, 5.0, 3.2, "HTTP Client\nPOST /ai/chat\nPOST /ai/classify", (5.8, 4.0), arrowstyle="<->")
    
    # 4. Backend to External services
    draw_arrow(ax, 7.2, 5.7, 8.4, 7.2, "NIM Validation", (7.8, 6.6), arrowstyle="->")
    draw_arrow(ax, 7.2, 5.7, 8.4, 5.4, "Upload Modul", (7.8, 5.7), arrowstyle="->")
    
    # 5. FastAPI to External & DB
    draw_arrow(ax, 7.8, 2.2, 8.4, 2.5, "Inference Prompts", (8.1, 2.6), arrowstyle="<->")
    draw_arrow(ax, 4.3, 2.2, 3.3, 2.2, "pymongo Query\n(Mood History)", (3.8, 2.6), arrowstyle="->", connectionstyle="arc3,rad=-0.2")

    # Save
    plt.tight_layout()
    plt.savefig(os.path.join(OUTPUT_DIR, "arsitektur_keseluruhan.png"), dpi=300, bbox_inches='tight')
    plt.savefig(os.path.join(OUTPUT_DIR, "arsitektur_keseluruhan.svg"), format='svg', bbox_inches='tight')
    plt.close()

# ==============================================================================
# DIAGRAM 2: ARSITEKTUR FLUTTER APP
# ==============================================================================
def generate_diagram_2():
    fig, ax = plt.subplots(figsize=(10, 8))
    ax.set_xlim(0, 10)
    ax.set_ylim(0, 10)
    ax.axis('off')
    
    # Title
    ax.text(5, 9.6, "DIAGRAM 2: ARSITEKTUR LAYER FLUTTER (EMOLENS MOBILE)", 
            fontsize=13, fontweight='bold', ha='center')
            
    # Container Presentation Layer
    rect_pres = patches.Rectangle((0.5, 5.2), 9.0, 3.8, linewidth=1, edgecolor='#7E8E64', facecolor='#F1F8E9', linestyle='--', zorder=1)
    ax.add_patch(rect_pres)
    ax.text(0.7, 8.7, "PRESENTATION LAYER (UI & State)", fontsize=10, color='#33691E', fontweight='bold', zorder=2)
    
    # Presentation Layer Subcomponents
    draw_box(ax, "SCREENS / PAGES\n(UI Views)\n- LoginScreen\n- DashboardScreen\n- ChatRoomScreen\n- MoodTrackerScreen\n- StoriesScreen\n- ModulesScreen", 
             1.0, 5.6, 3.5, 2.8, facecolor='#9BAA7F', edgecolor='#7E8E64', textcolor='white', fontweight='bold', fontsize=9)
             
    draw_box(ax, "REUSABLE WIDGETS\n(Custom UI Components)\n- ButtonWidget\n- CardMood\n- GenderDialog\n- ThemeChooser", 
             5.5, 6.8, 3.5, 1.6, facecolor='#A9DFBF', edgecolor='#27AE60', textcolor='#1E8449', fontsize=9)
             
    # State Managers
    draw_box(ax, "STATE MANAGERS\n- ThemeManager (notifyListeners)\n- MusicPlayerManager (Audio Controller)", 
             5.5, 5.6, 3.5, 1.0, facecolor='#FFCDD2', edgecolor='#E53935', textcolor='#B71C1C', fontsize=9)

    # --- SERVICE LAYER ---
    rect_serv = patches.Rectangle((0.5, 2.6), 9.0, 2.2, linewidth=1, edgecolor='#00796B', facecolor='#E0F2F1', linestyle='--', zorder=1)
    ax.add_patch(rect_serv)
    ax.text(0.7, 4.5, "SERVICE LAYER (Business Logic & Auth Domain)", fontsize=10, color='#004D40', fontweight='bold', zorder=2)
    
    services_text = (
        "LaravelAuthService          |          LaravelSessionService (Token storage)\n"
        "AiService (AI Engine Proxy)  |          MoodService (Check-in recorder)\n"
        "StoryService (Social Share)   |          NotificationService (Daily Scheduler)\n"
        "CisService (Campus integration) |        SystemService (Device cleanup)"
    )
    draw_box(ax, f"SERVICES\n\n{services_text}", 
             1.0, 2.9, 8.0, 1.4, facecolor='#009688', edgecolor='#00796B', textcolor='white', fontsize=9)

    # --- DATA LAYER (API & LOCAL CONFIG) ---
    rect_data = patches.Rectangle((0.5, 0.4), 9.0, 1.8, linewidth=1, edgecolor='#303F9F', facecolor='#E8EAF6', linestyle='--', zorder=1)
    ax.add_patch(rect_data)
    ax.text(0.7, 1.9, "DATA LAYER (API & Device storage)", fontsize=10, color='#1A237E', fontweight='bold', zorder=2)
    
    draw_box(ax, "ApiConfig\n(baseUrl, headers)", 1.0, 0.6, 2.8, 1.1, facecolor='#3F51B5', edgecolor='#303F9F', textcolor='white', fontsize=9)
    draw_box(ax, "http client (HTTP requests)\n(POST/GET with Bearer AccessToken)", 4.2, 0.6, 2.8, 1.1, facecolor='#3F51B5', edgecolor='#303F9F', textcolor='white', fontsize=9)
    draw_box(ax, "flutter_local_notifications\n(Daily alarm/Doze-safe reminders)", 7.2, 0.6, 2.0, 1.1, facecolor='#3F51B5', edgecolor='#303F9F', textcolor='white', fontsize=9.5)

    # Arrows
    draw_arrow(ax, 2.7, 5.6, 2.7, 4.3, "Pemicu aksi / Membaca data", (2.7, 5.0), arrowstyle="->")
    draw_arrow(ax, 7.2, 5.6, 7.2, 4.3, "State updates / Auth checking", (7.2, 5.0), arrowstyle="->")
    draw_arrow(ax, 5.0, 2.9, 5.0, 1.7, "Panggilan API", (5.0, 2.3), arrowstyle="->")
    draw_arrow(ax, 8.2, 2.9, 8.2, 1.7, "Jadwalkan Pengingat", (8.2, 2.3), arrowstyle="->")
    
    # Save
    plt.tight_layout()
    plt.savefig(os.path.join(OUTPUT_DIR, "arsitektur_flutter.png"), dpi=300, bbox_inches='tight')
    plt.savefig(os.path.join(OUTPUT_DIR, "arsitektur_flutter.svg"), format='svg', bbox_inches='tight')
    plt.close()

# ==============================================================================
# DIAGRAM 3: ARSITEKTUR BACKEND (LARAVEL MVC)
# ==============================================================================
def generate_diagram_3():
    fig, ax = plt.subplots(figsize=(10, 8))
    ax.set_xlim(0, 10)
    ax.set_ylim(0, 10)
    ax.axis('off')
    
    # Title
    ax.text(5, 9.6, "DIAGRAM 3: ARSITEKTUR BACKEND MVC (DEL CARE LARAVEL)", 
            fontsize=13, fontweight='bold', ha='center')
            
    # Request entry point
    draw_box(ax, "MOBILE CLIENT\n(EMOLENS Flutter App)", 1.0, 8.2, 2.4, 0.9, facecolor='#E8F8F5', edgecolor='#117A65', textcolor='#0E6251', fontsize=9.5)
    draw_box(ax, "KONSOLOR BROWSER\n(Del Care Web UI)", 6.6, 8.2, 2.4, 0.9, facecolor='#EBF5FB', edgecolor='#2980B9', textcolor='#1B4F72', fontsize=9.5)
    
    # --- ROUTES LAYER ---
    rect_routes = patches.Rectangle((0.5, 6.4), 9.0, 1.3, linewidth=1, edgecolor='#616161', facecolor='#F5F5F5', linestyle='--', zorder=1)
    ax.add_patch(rect_routes)
    ax.text(0.7, 7.4, "ROUTES / ENTRANCE LAYER", fontsize=10, color='#212121', fontweight='bold', zorder=2)
    
    draw_box(ax, "routes/api.php\n(Sanctum token-based authentication middleware)", 1.0, 6.6, 3.8, 0.9, facecolor='#795548', edgecolor='#5D4037', textcolor='white', fontsize=9)
    draw_box(ax, "routes/web.php\n(Web Session & CSRF protection cookie-based middleware)", 5.2, 6.6, 3.8, 0.9, facecolor='#795548', edgecolor='#5D4037', textcolor='white', fontsize=9)

    # --- CONTROLLERS ---
    rect_ctrl = patches.Rectangle((0.5, 4.4), 9.0, 1.7, linewidth=1, edgecolor='#00796B', facecolor='#E0F2F1', linestyle='--', zorder=1)
    ax.add_patch(rect_ctrl)
    ax.text(0.7, 5.8, "CONTROLLERS (Request Handlers & Coordinators)", fontsize=10, color='#004D40', fontweight='bold', zorder=2)
    
    draw_box(ax, "API CONTROLLERS\n- AuthController\n- MoodController\n- StoryController\n- AiController\n- NotificationController", 
             1.0, 4.6, 3.8, 1.1, facecolor='#009688', edgecolor='#00796B', textcolor='white', fontsize=8.5)
             
    draw_box(ax, "WEB CONTROLLERS\n- DashboardController (Scan & List)\n- LaporanController (Reports)\n- AdminController (Schedules & Slots)", 
             5.2, 4.6, 3.8, 1.1, facecolor='#009688', edgecolor='#00796B', textcolor='white', fontsize=8.5)

    # --- SERVICE LAYER (Third-Party proxies) ---
    rect_serv = patches.Rectangle((0.5, 2.4), 9.0, 1.7, linewidth=1, edgecolor='#B7950B', facecolor='#FEF9E7', linestyle='--', zorder=1)
    ax.add_patch(rect_serv)
    ax.text(0.7, 3.8, "SERVICES LAYER (Integrasi FastAPI & Cloud)", fontsize=10, color='#7D6608', fontweight='bold', zorder=2)
    
    draw_box(ax, "App\Services\AiService\n(FastAPI Client Gateway)\n- ping() / status()\n- getChatResponse() / generate-popup\n- getRecommendation() / recommend\n- classifyText() / classify", 
             1.0, 2.6, 5.0, 1.1, facecolor='#D4AC0D', edgecolor='#9A7D0A', textcolor='#1C2833', fontsize=8.5)
             
    draw_box(ax, "CIS / Cloudinary SDK Clients\n(External API calls for auth & media)", 6.4, 2.6, 2.6, 1.1, facecolor='#D4AC0D', edgecolor='#9A7D0A', textcolor='#1C2833', fontsize=8.5)

    # --- MODELS & DATABASES (DUAL DB ORM) ---
    rect_models = patches.Rectangle((0.5, 0.4), 9.0, 1.7, linewidth=1, edgecolor='#1F618D', facecolor='#EBF5FB', linestyle='--', zorder=1)
    ax.add_patch(rect_models)
    ax.text(0.7, 1.8, "MODELS & DUAL-DATABASE ORM TIER", fontsize=10, color='#1A5276', fontweight='bold', zorder=2)
    
    draw_box(ax, "Laravel-MongoDB Model (NoSQL)\n(Student, DailyCheckin, Story, Module)\n-> MongoDB Atlas: 'monitoring' DB", 
             1.0, 0.6, 3.8, 1.0, facecolor='#2980B9', edgecolor='#1F618D', textcolor='white', fontsize=8.5)
             
    draw_box(ax, "Eloquent RDBMS Model (SQL)\n(User, JadwalKonseling, Laporan, Notifikasi)\n-> MySQL / SQLite: 'delcare' DB", 
             5.2, 0.6, 3.8, 1.0, facecolor='#2980B9', edgecolor='#1F618D', textcolor='white', fontsize=8.5)

    # Arrows
    draw_arrow(ax, 2.2, 8.2, 2.2, 7.5, "API Request", (2.2, 7.8), arrowstyle="->")
    draw_arrow(ax, 7.8, 8.2, 7.8, 7.5, "Web Request", (7.8, 7.8), arrowstyle="->")
    draw_arrow(ax, 2.5, 6.6, 2.5, 5.7, "Route to Controller", (2.5, 6.2), arrowstyle="->")
    draw_arrow(ax, 7.5, 6.6, 7.5, 5.7, "Route to Controller", (7.5, 6.2), arrowstyle="->")
    
    # Controllers call models and services
    draw_arrow(ax, 2.9, 4.6, 2.9, 3.7, "Query / Send payload", (2.9, 4.2), arrowstyle="->")
    draw_arrow(ax, 7.1, 4.6, 7.1, 3.7, "API call validation", (7.1, 4.2), arrowstyle="->")
    
    # Services/Models to DB
    draw_arrow(ax, 2.9, 2.6, 2.9, 1.6, "CRUD operations", (2.9, 2.1), arrowstyle="->")
    draw_arrow(ax, 7.1, 2.6, 7.1, 1.6, "CRUD operations", (7.1, 2.1), arrowstyle="->")
    
    # Save
    plt.tight_layout()
    plt.savefig(os.path.join(OUTPUT_DIR, "arsitektur_backend.png"), dpi=300, bbox_inches='tight')
    plt.savefig(os.path.join(OUTPUT_DIR, "arsitektur_backend.svg"), format='svg', bbox_inches='tight')
    plt.close()

# ==============================================================================
# DIAGRAM 4: ARSITEKTUR MACHINE LEARNING (FASTAPI AI ENGINE)
# ==============================================================================
def generate_diagram_4():
    fig, ax = plt.subplots(figsize=(10, 8))
    ax.set_xlim(0, 10)
    ax.set_ylim(0, 10)
    ax.axis('off')
    
    # Title
    ax.text(5, 9.6, "DIAGRAM 4: ARSITEKTUR ENGINE ML (DEL CARE FASTAPI ENGINE)", 
            fontsize=13, fontweight='bold', ha='center')
            
    # Input dari Laravel
    draw_box(ax, "LARAVEL API REQUESTS\n(Client payloads & parameters)", 3.2, 8.4, 3.6, 0.8, 
             facecolor='#FADBD8', edgecolor='#CD6155', textcolor='#78281F', fontweight='bold', fontsize=9.5)

    # --- ENDPOINTS LAYER ---
    rect_end = patches.Rectangle((0.5, 6.4), 9.0, 1.6, linewidth=1, edgecolor='#B7950B', facecolor='#FEF9E7', linestyle='--', zorder=1)
    ax.add_patch(rect_end)
    ax.text(0.7, 7.7, "FASTAPI API ENDPOINTS (FastAPI Router)", fontsize=10, color='#7D6608', fontweight='bold', zorder=2)
    
    draw_box(ax, "POST /api/classify\n(NLP & Predictive)", 0.8, 6.6, 1.7, 0.8, facecolor='#F39C12', edgecolor='#B7950B', textcolor='white', fontsize=8.5)
    draw_box(ax, "POST /api/generate-popup\n(Emotional Chat)", 2.6, 6.6, 1.7, 0.8, facecolor='#F39C12', edgecolor='#B7950B', textcolor='white', fontsize=8.5)
    draw_box(ax, "POST /api/recommend\n(Quote Engine)", 4.4, 6.6, 1.6, 0.8, facecolor='#F39C12', edgecolor='#B7950B', textcolor='white', fontsize=8.5)
    draw_box(ax, "GET /api/predictive-radar\n(Batch clinical risk radar)", 6.1, 6.6, 1.7, 0.8, facecolor='#F39C12', edgecolor='#B7950B', textcolor='white', fontsize=8.5)
    draw_box(ax, "POST /api/summarize\n(Monthly Jurnal Report)", 7.9, 6.6, 1.5, 0.8, facecolor='#F39C12', edgecolor='#B7950B', textcolor='white', fontsize=8.5)

    # --- PIPELINES LAYER ---
    rect_pipe = patches.Rectangle((0.5, 2.0), 9.0, 4.0, linewidth=1, edgecolor='#1B5E20', facecolor='#E8F5E9', linestyle='--', zorder=1)
    ax.add_patch(rect_pipe)
    ax.text(0.7, 5.7, "AI / ML PIPELINES", fontsize=10, color='#1B5E20', fontweight='bold', zorder=2)

    # NLP Severity Classification
    rect_nlp = patches.Rectangle((0.8, 2.2), 3.8, 3.2, linewidth=1, edgecolor='#4CAF50', facecolor='#E8F5E9', zorder=2)
    ax.add_patch(rect_nlp)
    ax.text(1.0, 5.1, "NLP Severity Classifier (classify_text)", fontsize=9, color='#1B5E20', fontweight='bold', zorder=3)
    
    draw_box(ax, "Step 1: Rule-Based Red Flag Matcher\n(Regex check suicidal ideation, self-harm)\n-> YES: Output Level 3 (Emergency, 100%)", 
             1.0, 4.1, 3.4, 0.8, facecolor='#FFEBEE', edgecolor='#EF5350', textcolor='#C62828', fontsize=7.5)
             
    draw_box(ax, "Step 2: IndoBERT Model Classifier\n(Fine-tuned BertForSequenceClassification)\n-> NO RED FLAG: Predicts Level 0-3 + confidence", 
             1.0, 2.5, 3.4, 0.8, facecolor='#E8F5E9', edgecolor='#4CAF50', textcolor='#1B5E20', fontsize=7.5)

    # Predictive Analytics Pipeline
    rect_pred = patches.Rectangle((4.8, 2.2), 4.4, 3.2, linewidth=1, edgecolor='#4CAF50', facecolor='#E8F5E9', zorder=2)
    ax.add_patch(rect_pred)
    ax.text(5.0, 5.1, "Predictive Risk Evaluator", fontsize=9, color='#1B5E20', fontweight='bold', zorder=3)
    
    draw_box(ax, "Mood Score Mapping\n- Mood/Feeling mapped to scores 1-5\n- Daily Checkin Score = (Mood + Feeling) / 2", 
             5.0, 4.1, 4.0, 0.8, facecolor='#FFF3E0', edgecolor='#FF9800', textcolor='#E65100', fontsize=7.5)
             
    draw_box(ax, "Trend & Inactivity Analyser\n- Short-term Trend: 3-day decline -> Level 1 or 2\n- Long-term: 14-day average >= 4.0 -> Level 3\n- Passive Alert: >=14d inactive + bad history", 
             5.0, 2.5, 4.0, 1.4, facecolor='#FFF3E0', edgecolor='#FF9800', textcolor='#E65100', fontsize=7.5)

    # --- INFERENCE & STORAGE LAYER ---
    rect_inf = patches.Rectangle((0.5, 0.4), 9.0, 1.3, linewidth=1, edgecolor='#1F618D', facecolor='#EBF5FB', linestyle='--', zorder=1)
    ax.add_patch(rect_inf)
    ax.text(0.7, 1.4, "INFERENCE CLIENTS & DIRECT ATTACHMENTS", fontsize=10, color='#1A5276', fontweight='bold', zorder=2)
    
    draw_box(ax, "Local Folder: 'pa3_indobert_final'\n(Saved tokenizer & model pytorch weights)", 0.8, 0.6, 3.5, 0.7, facecolor='#3498DB', edgecolor='#2980B9', textcolor='white', fontsize=8.5)
    draw_box(ax, "Groq Python SDK Client\n(Llama 3.3-70b-versatile cloud API)", 4.5, 0.6, 2.5, 0.7, facecolor='#3498DB', edgecolor='#2980B9', textcolor='white', fontsize=8.5)
    draw_box(ax, "pymongo Database Connector\n-> MongoDB Atlas Connection", 7.2, 0.6, 2.1, 0.7, facecolor='#3498DB', edgecolor='#2980B9', textcolor='white', fontsize=8.5)

    # Arrows
    draw_arrow(ax, 5.0, 8.4, 5.0, 7.4, "Payload parsing", (5.0, 7.9), arrowstyle="->")
    
    # From endpoints to pipelines
    draw_arrow(ax, 1.6, 6.6, 1.6, 5.6, "Request values", (1.6, 6.0), arrowstyle="->")
    draw_arrow(ax, 5.2, 6.6, 5.2, 5.6, "Mood histories", (5.2, 6.0), arrowstyle="->")
    
    # From pipelines to models/DB
    draw_arrow(ax, 2.7, 2.5, 2.7, 1.3, "Loads model weights", (2.7, 1.9), arrowstyle="->")
    draw_arrow(ax, 8.2, 2.5, 8.2, 1.3, "Direct fetch Radar", (8.2, 1.9), arrowstyle="->")

    # Save
    plt.tight_layout()
    plt.savefig(os.path.join(OUTPUT_DIR, "arsitektur_ml.png"), dpi=300, bbox_inches='tight')
    plt.savefig(os.path.join(OUTPUT_DIR, "arsitektur_ml.svg"), format='svg', bbox_inches='tight')
    plt.close()

# ==============================================================================
# DIAGRAM 5: ALUR INTEGRASI (SEQUENCE WORKFLOW)
# ==============================================================================
def generate_diagram_5():
    fig, ax = plt.subplots(figsize=(12, 10))
    ax.set_xlim(0, 15)
    ax.set_ylim(0, 11)
    ax.axis('off')
    
    # Title
    ax.text(7.5, 10.5, "DIAGRAM 5: ALUR INTEGRASI SISTEM KESELURUHAN (SEQUENCE DIAGRAM)", 
            fontsize=13, fontweight='bold', ha='center')
            
    # X Coordinates for Lifelines
    X_FLUTTER = 1.5
    X_LARAVEL = 5.0
    X_FASTAPI = 8.5
    X_DB = 11.5
    X_KONSELOR = 14.0
    
    # Draw Actor Boxes
    draw_box(ax, "Mahasiswa\n(Flutter App)", X_FLUTTER-1, 9.2, 2.0, 0.8, facecolor='#9BAA7F', edgecolor='#7E8E64', textcolor='white', fontweight='bold', fontsize=9)
    draw_box(ax, "Del Care\n(Laravel Backend)", X_LARAVEL-1.2, 9.2, 2.4, 0.8, facecolor='#009688', edgecolor='#00796B', textcolor='white', fontweight='bold', fontsize=9)
    draw_box(ax, "AI Engine\n(FastAPI)", X_FASTAPI-1, 9.2, 2.0, 0.8, facecolor='#F39C12', edgecolor='#B7950B', textcolor='white', fontweight='bold', fontsize=9)
    draw_box(ax, "Databases\n(MySQL & Mongo)", X_DB-1.2, 9.2, 2.4, 0.8, facecolor='#2980B9', edgecolor='#1F618D', textcolor='white', fontweight='bold', fontsize=9)
    draw_box(ax, "Konselor\n(Web Dashboard)", X_KONSELOR-1.2, 9.2, 2.4, 0.8, facecolor='#3F51B5', edgecolor='#303F9F', textcolor='white', fontweight='bold', fontsize=9)
    
    # Draw Lifelines
    for x in [X_FLUTTER, X_LARAVEL, X_FASTAPI, X_DB, X_KONSELOR]:
        ax.plot([x, x], [0.5, 9.2], color='#999999', linestyle='--', linewidth=1.5, zorder=1)
        
    def seq_arrow(y, x_start, x_end, text, color='#333333'):
        # Arrow line
        ax.annotate("", xy=(x_end, y), xytext=(x_start, y), 
                    arrowprops=dict(arrowstyle="->", color=color, linewidth=1.5, zorder=3))
        # Text
        mid_x = (x_start + x_end) / 2
        y_offset = 0.15
        ax.text(mid_x, y + y_offset, text, fontsize=8, color=color, ha='center', va='bottom', 
                backgroundcolor='white', zorder=4)

    # Sequence Steps
    y = 8.5
    seq_arrow(y, X_FLUTTER, X_LARAVEL, "1. POST /api/mood/check-in\n(Data Jurnal & Mood)", '#1B5E20')
    
    y -= 0.8
    seq_arrow(y, X_LARAVEL, X_DB, "2. Insert raw data\n(MongoDB & MySQL)", '#0D47A1')
    
    y -= 0.8
    seq_arrow(y, X_LARAVEL, X_FASTAPI, "3. POST /ai/classify\n(Kirim teks jurnal)", '#E65100')
    
    y -= 0.8
    # Self arrow for FastAPI
    ax.annotate("", xy=(X_FASTAPI+0.1, y-0.3), xytext=(X_FASTAPI+0.1, y), 
                arrowprops=dict(connectionstyle="arc3,rad=-0.5", arrowstyle="->", color='#E65100', linewidth=1.5, zorder=3))
    ax.text(X_FASTAPI + 0.6, y - 0.15, "4. IndoBERT NLP & Regex\n(Prediksi Severity)", fontsize=8, color='#E65100', va='center', backgroundcolor='white')
    
    y -= 0.8
    seq_arrow(y, X_FASTAPI, X_LARAVEL, "5. Return Prediction\n(Level 0-3, Flags)", '#E65100')
    
    y -= 0.8
    seq_arrow(y, X_LARAVEL, X_DB, "6. Update record with Result", '#0D47A1')
    
    y -= 0.8
    # Self arrow for Laravel
    ax.annotate("", xy=(X_LARAVEL+0.1, y-0.3), xytext=(X_LARAVEL+0.1, y), 
                arrowprops=dict(connectionstyle="arc3,rad=-0.5", arrowstyle="->", color='#00796B', linewidth=1.5, zorder=3))
    ax.text(X_LARAVEL + 0.6, y - 0.15, "7. Evaluasi Risiko\n(Apakah Level >= 2?)", fontsize=8, color='#00796B', va='center', backgroundcolor='white')
    
    y -= 0.8
    seq_arrow(y, X_LARAVEL, X_DB, "8. Insert Urgent Notification\n(If Level 3)", '#C62828')
    
    y -= 0.8
    seq_arrow(y, X_DB, X_KONSELOR, "9. Polling / View Notifikasi\n(Web Dashboard Alerts)", '#C62828')
    
    y -= 0.8
    seq_arrow(y, X_LARAVEL, X_FLUTTER, "10. HTTP 200 OK\n(Check-in Success + AI Response)", '#1B5E20')
    
    y -= 0.8
    # Self arrow for Flutter
    ax.annotate("", xy=(X_FLUTTER-0.1, y-0.3), xytext=(X_FLUTTER-0.1, y), 
                arrowprops=dict(connectionstyle="arc3,rad=0.5", arrowstyle="->", color='#33691E', linewidth=1.5, zorder=3))
    ax.text(X_FLUTTER - 0.3, y - 0.15, "11. Tampilkan UI Pop-up\n(Rekomendasi/Peringatan)", fontsize=8, color='#33691E', ha='right', va='center', backgroundcolor='white')

    # Save
    plt.tight_layout()
    plt.savefig(os.path.join(OUTPUT_DIR, "alur_integrasi.png"), dpi=300, bbox_inches='tight')
    plt.savefig(os.path.join(OUTPUT_DIR, "alur_integrasi.svg"), format='svg', bbox_inches='tight')
    plt.close()

def generate_composite_diagram():
    import matplotlib.image as mpimg
    print("[RUNNING] Generating Composite 2x2 Grid Diagram...")
    fig, axs = plt.subplots(2, 2, figsize=(20, 16))
    
    img1 = mpimg.imread(os.path.join(OUTPUT_DIR, "arsitektur_keseluruhan.png"))
    img2 = mpimg.imread(os.path.join(OUTPUT_DIR, "arsitektur_flutter.png"))
    img3 = mpimg.imread(os.path.join(OUTPUT_DIR, "arsitektur_backend.png"))
    img4 = mpimg.imread(os.path.join(OUTPUT_DIR, "arsitektur_ml.png"))
    
    axs[0, 0].imshow(img1)
    axs[0, 0].set_title("A. Arsitektur Sistem Keseluruhan", fontsize=16, fontweight='bold', pad=10)
    axs[0, 0].axis('off')
    
    axs[0, 1].imshow(img2)
    axs[0, 1].set_title("B. Arsitektur Layer Flutter App", fontsize=16, fontweight='bold', pad=10)
    axs[0, 1].axis('off')
    
    axs[1, 0].imshow(img3)
    axs[1, 0].set_title("C. Arsitektur Backend MVC (Laravel)", fontsize=16, fontweight='bold', pad=10)
    axs[1, 0].axis('off')
    
    axs[1, 1].imshow(img4)
    axs[1, 1].set_title("D. Arsitektur Machine Learning Engine (FastAPI)", fontsize=16, fontweight='bold', pad=10)
    axs[1, 1].axis('off')
    
    plt.tight_layout(pad=3.0)
    plt.savefig(os.path.join(OUTPUT_DIR, "arsitektur_gabungan_2x2.png"), dpi=150, bbox_inches='tight')
    plt.savefig(os.path.join(OUTPUT_DIR, "arsitektur_gabungan_2x2.svg"), format='svg', bbox_inches='tight')
    plt.close()

if __name__ == "__main__":
    print("[RUNNING] Generating Architecture Diagrams...")
    generate_diagram_1()
    print("[SUCCESS] Diagram 1: Overall System Architecture generated.")
    generate_diagram_2()
    print("[SUCCESS] Diagram 2: Flutter Layer Architecture generated.")
    generate_diagram_3()
    print("[SUCCESS] Diagram 3: Laravel Backend MVC Architecture generated.")
    generate_diagram_4()
    print("[SUCCESS] Diagram 4: ML FastAPI Engine Architecture generated.")
    generate_diagram_5()
    print("[SUCCESS] Diagram 5: Integration Workflow Sequence generated.")
    generate_composite_diagram()
    print("[SUCCESS] Diagram 5: Composite 2x2 Grid Architecture generated.")
    print(f"[COMPLETE] All diagrams saved in PNG and SVG formats inside: {OUTPUT_DIR}")
