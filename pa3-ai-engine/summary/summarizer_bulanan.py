import os
from groq import Groq
from dotenv import load_dotenv

BASE_DIR = os.path.dirname(os.path.abspath(__file__))

# Path ke file .env di dalam folder pa3-ai-engine (prioritas utama)
env_path = os.path.join(BASE_DIR, '..', '.env')
load_dotenv(dotenv_path=env_path, override=True)

GROQ_MODEL = os.getenv("GROQ_MODEL", "llama-3.3-70b-versatile")
api_key    = os.getenv("GROQ_API_KEY")

if api_key and api_key.startswith("gsk_"):
    client = Groq(api_key=api_key)
    print(f"Summarizer: Groq client siap menggunakan model {GROQ_MODEL}.")
else:
    client = None
    print(f"Warning: GROQ_API_KEY tidak valid ({api_key[:10] if api_key else 'None'}...) — summarizer fallback ke mode sederhana.")

def _summarize_with_groq(semua_jurnal: list) -> str:
    """Kirim semua jurnal sekaligus ke Groq LLaMA untuk diringkas."""

    # Format jurnal dengan nomor urut agar AI tahu ada berapa jurnal
    formatted = "\n".join([
        f"Jurnal {i + 1}: {text.strip()}"
        for i, text in enumerate(semua_jurnal)
    ])

    try:
        response = client.chat.completions.create(
            messages=[
                {
                    "role": "system",
                    "content": (
                        "Kamu adalah seorang psikolog klinis ahli. "
                        "Tugasmu adalah menganalisis kumpulan jurnal harian mahasiswa untuk memberikan INSIGHT MENDALAM bagi konselor kampus. "
                        "Jangan hanya merangkum isi jurnal, tapi analisislah pola psikologisnya. "
                        "Fokus pada: "
                        "1. Tren Emosional: Apakah ada peningkatan kecemasan, tanda-tanda depresi, atau justru stabilitas? "
                        "2. Pola Perilaku & Koping: Bagaimana mahasiswa menghadapi masalahnya? Apakah kopingnya sehat atau maladaptif? "
                        "3. Isu Berulang: Identifikasi masalah inti yang terus muncul (misal: tekanan akademik, masalah keluarga, atau isolasi sosial). "
                        "4. Rekomendasi Pendekatan: Berikan saran singkat bagi konselor cara mendekati mahasiswa ini. "
                        "Tulis dalam Bahasa Indonesia yang profesional, empati, dan padat (maksimal 5 kalimat). "
                        "Gunakan format paragraf mengalir. JANGAN gunakan bullet points, angka, atau format markdown."
                    )
                },
                {
                    "role": "user",
                    "content": (
                        f"Berikut adalah {len(semua_jurnal)} jurnal harian mahasiswa bulan ini:\n\n"
                        f"{formatted}\n\n"
                        "Buatkan ringkasan kondisi psikologis mahasiswa berdasarkan SEMUA jurnal di atas."
                    )
                }
            ],
            model=GROQ_MODEL,
            temperature=0.4,
        )
        return response.choices[0].message.content.strip()

    except Exception as e:
        print(f"Groq summarizer error: {e} — fallback ke mode sederhana")
        return _fallback_summary(semua_jurnal)


def _fallback_summary(semua_jurnal: list) -> str:
    """Fallback sederhana jika Groq tidak tersedia."""
    cuplikan = " | ".join(semua_jurnal[:3])
    suffix   = "..." if len(semua_jurnal) > 3 else ""
    return (
        f"Mahasiswa menulis {len(semua_jurnal)} jurnal bulan ini. "
        f"Cuplikan: {cuplikan}{suffix}"
    )

def proses_jurnal_sebulan(list_jurnal: list) -> str:
    """
    Meringkas seluruh jurnal dalam satu bulan menjadi satu kesimpulan.

    Args:
        list_jurnal (list[str]): Seluruh teks jurnal dalam periode yang dipilih.

    Returns:
        str: Teks ringkasan kondisi psikologis mahasiswa.
    """
    print(f"\nMemproses total {len(list_jurnal)} entri jurnal harian...")

    if not client:
        print("Mode fallback: Groq tidak tersedia.")
        return _fallback_summary(list_jurnal)

    print("[AI Summarizer] Mengirim semua jurnal ke Groq LLaMA untuk diringkas...")
    hasil = _summarize_with_groq(list_jurnal)

    print("\n" + "=" * 60)
    print("KESIMPULAN KONDISI MAHASISWA BULAN INI")
    print("=" * 60)
    print(hasil)
    print("=" * 60)

    return hasil


def generate_monthly_summary(nim: str, journal_texts: list) -> str:
    """
    Fungsi utama — dipanggil oleh main.py.
    Menerima teks jurnal langsung dari Laravel (hasil query database).

    Args:
        nim (str): NIM mahasiswa (untuk logging).
        journal_texts (list[str]): Seluruh teks jurnal dari database.

    Returns:
        str: Teks ringkasan kondisi psikologis bulanan.
    """
    if not journal_texts:
        return "Belum ada data jurnal untuk periode ini."

    print(f"\n[Summarizer] Memproses {len(journal_texts)} jurnal untuk NIM: {nim}")
    return proses_jurnal_sebulan(journal_texts)


# =====================================================================
# SIMULASI — hanya berjalan saat file dijalankan langsung
# =====================================================================
if __name__ == "__main__":
    contoh_jurnal = [
        "Hari ini merasa sangat cemas karena deadline PKM dan tugas akhir makin dekat.",
        "Revisi sistem manajemen ternyata banyak banget. Rasanya capek dan ingin menyerah.",
        "Masih kepikiran soal revisi kemarin. Takut kalau sidang ditanya hal yang belum kupahami.",
        "Akhirnya proposal ACC! Lega banget bisa tidur nyenyak malam ini.",
    ]
    hasil = generate_monthly_summary("MHS-TEST", contoh_jurnal)
    print(f"\nHasil: {hasil}")