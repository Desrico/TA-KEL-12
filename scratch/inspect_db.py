import os
from pymongo import MongoClient
from dotenv import load_dotenv

load_dotenv()

mongo_uri = os.getenv("MONGODB_URI")
db_name = os.getenv("MONGODB_DATABASE", "test")

client = MongoClient(mongo_uri)
db = client[db_name]

print(f"Connecting to database: {db_name}")
collections = db.list_collection_names()
print(f"Collections: {collections}")

for coll_name in collections:
    print(f"\n--- Collection: {coll_name} ---")
    sample = list(db[coll_name].find().limit(2))
    for doc in sample:
        print(doc)
