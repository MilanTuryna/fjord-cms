**Note
FjordCMS je aktuálně ve vývoji. Průběh vývoje můžete sledovat v reálném čase pomocí historie commitů a nebo také screenshotů (již brzy).

# Fjord-CMS

Webový systém určený pro tvoření originálních webů rychleji, efektivněji.  Disponuje modularitou a výsledkem jsou weby s libovolnou šablonou a různorodým zaměřením.
**Použité backend technologie:** PHP (Nette), MySQL, JSON

**Co je cílem?**
-   Vytvoření webového systému s rychlým nasazením na weby různorodých zájmů a funkcionalit
-   Webový systém s podporou aktualizací a rozšíření
-  Webový systém s možností tvorby virtuální databáze pro uchovávání různých dat na základě aktuálního uživatele webového systému (viz EAV)
-   Webový systém s podporou multijazyčnosti

# Datové zpracování
Fjord CMS bude využívat k datovému zpracování a přístupu k databázi MySQL avšak s odlišným přístupem jak je tomu zvyklé. Kvůli modularitě, kterou Fjord CMS podporuje, je nutné ke všemu přistupovat dynamicky a tak i přemýšlet, abychom výsledkem byl nikdy nezastaralý rozvíjející redakční systém. Budou využity principy jako je EAV. Níže jsou popsány jednotlivé tabulky a jejich funkce či smysl:
## EAV (Vytváření vlastních entit)
Architektura EAV je využívána pro vytváření dynamických entit a položek, které ukládat a u kterých následně chceme vypisovat určitá data či informace. Tyto dynamické entity bude možné vytvářet v nastavení redakčního systému administrátorem s příslušným oprávněním.

**Uložení vlastních entit**
-   fjord_dynamic_attributes - Tabulka spravující ukládané hodnoty dané entity
-   fjord_dynamic_values - Tabulka spravující reálné data dané entity
-   fjord_dynamic_entities - Tabulka spravující danou entitu (virtuální tabulku)
-  fjord_dynamic_ids - Tabulka spravující řádky a interpretace jednotlivých entit pomocí ID

## Multijazyčnost
Důležitou součástí FjordCMS je překládání jednotlivých hodnot do různých jazyků a zprovoznění multijazyčnosti. Multijazyčnost je řešena způsobem, kdy každý sloupec s možností nastavení jazykové interpretace, bude JSON ve formátu:

    {
      "<language>": "value"
    }
**Řešení situací**
- V případě, že uživatel používá jazyk, který v daném slovníku nebyl nalezen, bude použit ten, který je nastaven jako základní. V případě, že nově nastavený základní jazyk nebude mít svůj překlad, bude nastaven ten, který byl posledně nastaven. V případě, že žádná z možností není vyhovující nebo se v překladu vyskytuje pouze jeden jazyk, použitý jazyk se nastavuje na zbylý jazyk.
# Administrace
Hlavním ovládacím prvkem Fjord CMS je uživatelsky přívětivá administrace rozdělena na dva logické celky pro odlišení administrace redakčního systému a obsahové správy.
- Nastavení redakčního systému- tvorba vlastních databázových entit, individuální přizpůsobení webu, tvorba widgetů, přidávání rozšíření
-  Správa obsahu: redaktorské prostředí s využitím již nastaveného CMS, správa galerie, nastavení obsahu celkového webu, využití widgetů

## Vytváření vlastních entit
Důležitým prvkem administrace je uživatelsky přívětivé zpracování vytváření vlastních dynamických entit a užití modelu EAV. Toto je řešeno komplexním formulářem, viz:
-   Název vlastní entity/položky: (př. článek)
-   Popis vlastní entity
-   Atributy (dynamický prvek/přidavatelný)
    -   Název atributu /text-input
    -   Datový typ atributu /select-input
        -   Číslo (integer)
        -   Číslo s desetinnými místy (float)
        -   Textový řetězec (string)
        -   Textový řetězec s překladem (longtext{TranslatedValue|JSON})    
        -   ANO/NE - pravda (bool)
    -   Popis atributu /text-input (bude zobrazen při následném obsahovém použití)
    -   Nastavení placeholderu /text-input
    -   Přednastavení hodnoty: výběr z dvou možností, omezit druhou v případě jedné
        -   Vlastní hodnoty
        -   Vygenerované: <created, edited, edited_admin, created_admin>
    -   Je atribut povinný? /checkbox


## Oprávnění a práva
Administrace disponuje systémem oprávnění na bázi tzv. permisí pro možnost udržení určité hierarchie ve správě administrace. Každá logická část má svou permisi, která je vlastněna administrátorem, dle toho, jaké práva mu byli přiřazeny. Nejvyšší práva má tzv. superuser, což je administrátor s plným oprávněním (*) a taky ten, kdo rozhoduje o ostatních administrátorech a jejich právy.

**Aktuální oprávnění v CMS**

**Budoucí návrhy**
- V rámci budoucích verzí je v případě podpora dynamicky nastavitelných permisí např. pro správu galerií či vlastních entit. (tzn. možnost povolit například editaci článků - editaci vlastní entity pouze vyhrazeným uživatelům, nikoliv všem administrátorům s možností spravování vlastních entit)

