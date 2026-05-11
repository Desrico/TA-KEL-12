import os
import sys
from groq import Groq
from dotenv import load_dotenv

# Path to the .env file
env_path = os.path.join(os.getcwd(), 'pa3-ai-engine', '.env')
load_dotenv(dotenv_path=env_path, override=True)

api_key = os.getenv("GROQ_API_KEY")
model = os.getenv("GROQ_MODEL", "llama-3.3-70b-versatile")

print(f"Testing Groq with Model: {model}")
print(f"API Key found: {'Yes' if api_key else 'No'}")

if not api_key:
    print("Error: No API Key found.")
    sys.exit(1)

try:
    client = Groq(api_key=api_key)
    response = client.chat.completions.create(
        messages=[
            {"role": "user", "content": "Say hello world briefly."}
        ],
        model=model,
    )
    print("Success! Response:")
    print(response.choices[0].message.content)
except Exception as e:
    print(f"Failed! Error: {e}")
