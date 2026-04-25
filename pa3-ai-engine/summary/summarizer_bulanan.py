import os
from groq import Groq
from dotenv import load_dotenv

BASE_DIR = os.path.dirname(os.path.abspath(__file__))

# Load API Key dari .env milik simulasi_ai_jurnal
load_dotenv(dotenv_path=os.path.join(BASE_DIR, '..', '.env'))

GROQ_MODEL = os.getenv("GROQ_MODEL", "llama-3.3-70b-versatile")
api_key    = os.getenv("GROQ_API_KEY")

if api_key:
    client = Groq(api_key=api_key)
    print("Summarizer: Groq client siap.")
else:
    client = None
    print("Warning: GROQ_API_KEY tidak ditemukan — summarizer fallback ke mode sederhana.")

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
                        "Kamu adalah asisten konselor kesehatan mental kampus. "
                        "Tugasmu adalah membuat ringkasan kondisi psikologis mahasiswa "
                        "berdasarkan SELURUH kumpulan jurnal harian yang mereka tulis dalam satu bulan. "
                        "Ringkasan harus mencakup: "
                        "(1) tren emosi dan kondisi mahasiswa secara keseluruhan, "
                        "(2) masalah atau tekanan utama yang muncul berulang, "
                        "(3) gambaran umum kondisi psikologis mahasiswa bulan ini. "
                        "Tulis dalam bahasa Indonesia yang jelas dan formal, maksimal 4 kalimat. "
                        "JANGAN menggunakan bullet point, daftar, angka, atau format markdown. "
                        "Tulis sebagai paragraf mengalir yang enak dibaca."
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