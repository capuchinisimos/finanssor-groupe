from flask import Flask, jsonify
import requests
from bs4 import BeautifulSoup

app = Flask(__name__)

@app.route('/')
def home():
    return "Bienvenue sur l'API Flask ! Utilisez /api/jobs pour voir les données."

@app.route('/api/jobs', methods=['GET'])
def fetch_jobs():
    try:
        # URL de la page Indeed
        url = "https://fr.indeed.com/cmp/Mondial-Tv/jobs?jk=4a9365783dcc11fc&start=0"

        # Requête HTTP pour récupérer le contenu de la page
        headers = {
   "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36",
    "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8",
    "Accept-Encoding": "gzip, deflate, br",
    "Accept-Language": "en-US,en;q=0.9",
    "Connection": "keep-alive",
}

        response = requests.get(url, headers=headers)
        response.raise_for_status()  # Vérifie les erreurs HTTP

        # Analyser le HTML avec BeautifulSoup
        soup = BeautifulSoup(response.text, "html.parser")
        job_listings = soup.find_all("div", class_="job_seen_beacon")

        # Extraire les données des offres
        jobs = []
        for job in job_listings:
            title = job.find("h2", class_="jobTitle").text.strip()
            company = job.find("span", class_="companyName").text.strip()
            location = job.find("div", class_="companyLocation").text.strip()
            link_tag = job.find("a", class_="jcs-JobTitle")
            link = f"https://fr.indeed.com{link_tag['href']}" if link_tag else None

            jobs.append({
                "title": title,
                "company": company,
                "location": location,
                "link": link
            })

        return jsonify(jobs)

    except Exception as e:
        print("Erreur complète :", str(e))
        return jsonify({"error": f"Une erreur est survenue lors du scraping : {str(e)}"}), 500

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5000)
