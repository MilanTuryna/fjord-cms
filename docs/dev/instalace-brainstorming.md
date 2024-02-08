# Instalace systému

Po nahrání FjordCMS na webový server, správném nastavení prostředí a 
úspěšném prvním spuštění bude zobrazen vícekrokový uživatelsky přívětivý formulář s nastavením základních informací o novém projektu a jeho struktuře (výběr ze základních modulů, které lze nainstalovat) a připojení do databáze.

- Vícekrokový formulář
- Nastavení připojení do databáze
- Nastavení informací o novém projektu a jeho struktuře
- Kontrola zadaných údajů a následné řešení s nimi

## Jednotlivé kroky
Logické celky zabývajicí se správným nastavením nového projektu.

### 1. Vytvoření nového projektu

#### Inputy
- Název projektu
- Typ webové prezentace
- Zaměření (obor) projektu
- Viditelnost webové prezentace
  - [Privátní, Neveřejný, Veřejný]
- Popis projektu
- Důvod použití FjordCMS
- Autor projektu
  - Email
  - Jméno autora

### 2. Nastavení šablony a webové prezentace
- Nastavení rozhraní administračního panelu:
  - Primární barva
  - Sekundární barva
  - ... (do budoucna Tmavý/Světlý režim)
- Výběr šablony:
  - Výběr z online katalogu šablon
  - Výběr vlastní knihovny (Nahrát)
  - Systém bez webové prezentace (bude dostupna pouze administrace, vhodné např. pro správy a systémy)
- Kontrola šablony a jeho kódu + modulů a jeho kódu (zda bude funkční s verzí)

### 3. Instalace modulů
- Výpis základních modulů od FjordCMS 
- Výpis modulů instalovaných dle šablony (a možnost vypnutí těch nepovinných)
- Výběr z katalog modulů (možnost nainstalovat další)

### 4. Nastavení databázového připojení
V tuto chvíli je poskytnuto pouze řešení s databázovým serverem MySQL/MariaDB. 
Do budoucna bude možno pracovat s databázovými řešeními jako PostgreSQL, SQLite a další.
#### Inputy
- Typ databáze (MySQL/MariaDB)
- Port databázového serveru (zákl. 3306)
- Hostitel (hostname)
- Uživatel
- Heslo

Následné shrnutí jaké tabulky budou vytvořeny a SQL dotaz - pouze na rozkliknutí, aby to nekazilo uživatelský dojem.
Po zadání a odeslání těchto údajů bude provedena jejich kontrola v reálném čase (před spuštěním webu musí být databáze funkční) a následně generování tabulek a databázové struktury.

### 5. Shrnutí
- Shrnutí základních informací, šablony a modulů + databáze. Po schválení bude instalace dle těchto zadaných údajů provedena, vč. vytvoření tabulek v dané databázi.