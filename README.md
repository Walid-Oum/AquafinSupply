# Aquafin Supply

## Projectbeschrijving

Aquafin Supply is een webapplicatie ontwikkeld in **Laravel** voor het beheren van materialen en bestellingen binnen Aquafin.


Het systeem ondersteunt drie verschillende gebruikersrollen:

- Technieker
- Magazijnmedewerker
- Administrator

---

# Functionaliteiten

## Technieker

- Materialen bekijken
- Materialen zoeken, filteren en sorteren
- Materialen toevoegen aan het winkelmandje
- Hoeveelheden aanpassen in het winkelmandje
- Bestellingen plaatsen
- Eigen bestellingen bekijken
- Details van bestellingen bekijken
- Tickets aanmaken
- Eigen tickets opvolgen
- Overstromingsrisico bekijken
- Suggesties van materialen ontvangen op basis van het overstromingsrisico
- Herinneringsmelding ontvangen om het gastoestel op te laden en mee te nemen
- Eigen depot (locatie) gekoppeld aan het account
- Bestellingen ophalen in het gekoppelde depot

---

## Magazijnmedewerker

- Overzicht van alle bestellingen
- Status van bestellingen wijzigen
- Hoeveelheden van bestellingen aanpassen
- Voorraad beheren
- Materiaaldetails bekijken
- Tickets van techniekers behandelen
- Ticketstatus aanpassen
- Overstromingsrisico bekijken
- Voorraadplanning voorbereiden op basis van het overstromingsrisico
- Overzicht van bestellingen per depot

---

## Administrator

- Gebruikers beheren
- Gebruikers aanmaken en aanpassen
- Rollen beheren
- Gebruikers koppelen aan een locatie (depot)
- Materialen beheren (CRUD)
- Materialen activeren en deactiveren
- Minimumvoorraad beheren
- Alle bestellingen bekijken
- Details van bestellingen bekijken
- Overstromingsrisico raadplegen
- Algemene materiaalplanning ondersteunen

---

# Extra functionaliteiten

- Winkelmandje
- Ticketingsysteem
- Afbeeldingen voor materialen
- Minimumvoorraad waarschuwingen
- Sessiegebaseerde herinneringsmeldingen
- Rolgebaseerde navigatie
- Aquafin branding
- Eigen favicon
- Overstromingsrisico module
- Gebruikers gekoppeld aan een locatie (depot)
- Bestellingen gekoppeld aan het depot van de gebruiker
- Overstromingsrisico gebaseerd op de provincie van de locatie

---

# Gebruikte technologieën

- Laravel
- PHP
- Blade
- Tailwind CSS
- JavaScript
- HTML5
- CSS3

---

# Installatie

```bash
git clone <repository-url>

cd aquafin-supply

composer install

npm install

npm run dev

php artisan migrate

php artisan db:seed

php artisan storage:link

php artisan serve
```

---

# Gebruikersrollen

| Rol | Functionaliteit |
|------|----------------|
| Technieker | Materialen bestellen, tickets aanmaken en overstromingsrisico bekijken |
| Magazijnmedewerker | Bestellingen verwerken, voorraad beheren en tickets behandelen |
| Administrator | Gebruikers, locaties en materialen beheren |

---

# Externe bronnen

Tijdens de ontwikkeling van dit project werden externe hulpmiddelen gebruikt.

## AI

AI werd gebruikt als hulpmiddel voor:

- Ondersteuning bij het programmeren
- Debuggen van code
- Opzoeken van Laravel-oplossingen
- Genereren van het Aquafin-logo
- Genereren van de achtergrondafbeelding van de layout

