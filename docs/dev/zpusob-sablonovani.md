

# Způsob šablonování FjordCMS

FjordCMS jakožto modulární řešení redakčního systému vyžaduje možnost dynamicky spravovat šablony a upravovat tak vzhled webu a nebo i jejich funkcionalitu na základě oboru, kterým se daný web zabývá. Systém šablonování ve FjordCMS zároveň musí pracovat s modelem EAV, který umožňuje správcům webu v interním nastavení redakčního systému vytvářet virtuální tabulky a dále je vypisovat.

Př. vytvoření virtuální tabulky Article a virtuální tabulky RestaurantItems (v případě, že by se jednalo např. o web pro restaurace)



**Využité technologie:**

-   EAV

-   JSON - slouží pro specifikaci dat a nahrávání šablon do webového systému, zároveň také umožňuje odkazovat na jednotlivé latte soubory

-   Latte - systém pro psaní šablon, umožňuje využívat "API", které FjordCMS do šablony předá pomocí přednastavených proměnných a funkcionalit




**Specifikace a struktura šablony**

-   Specifikaci a strukturu šablony zajištuje JSON

-   Je nutné, aby struktura šablona odkazovala na nezávislé prvky, které se dají upravit i samostatně.


-   V případě, že by správce webu chtěl vytvořit vlastní šablonu již v rámci systému uživatelsky přívětivým formulářem a nechtěl by použít šablonu, která je již přednastavená jako celek.




## Jednotlivé šablony:

Každá šablona má určité nestrukturizované vlastnosti jakožto Jméno/Autor/Cíl šablony a dále logické vlastnosti kterými jsou nastavení jednotlivých stránek v šabloně vč. odkazu k jednotlivé latte stránkám, vyžadování specifických EAV entit.



**Nestrukturizované vlastnosti šablony:**

-   Název šablony - title - string*
    -   V případě duplicity v rámci jednoho redakčního systému bude k šabloně přidáno číslování (bude řešeno pomocí while cyklu, kdyby již bylo více duplicit)
-   Autor šablony - author - string
-   Name* - string
-   Email - string
-   Web - string
-   Popis a cíl šablony - description - string
-   Verze šablony - version - string
-   TODO: version_list_api

**Strukturizované vlastnosti šablony:**
-   Jednotlivé stránky - pages - asociativní pole - (pages.*jméno kategorie*)
    -   *Jméno kategorie* - array
        -   Název stránky - page_title - string
        -   Popis stránky - description - string
        -   Výstup - output - object
        - Obsah (závislý na typu) - content - string
            -   Typ výstupu - type - enum(SRC, PATH)/string

**Vyžadování specifických EAV entit**

Šablona může vyžadovat specifické EAV entity aby mohla fungovat, tak jak byla nadesignována a vytvořena. (Např. když bude šablona určena pro internetový obchod, je nutné zajistit, aby mohli být a tak naplněny specifickými a mohla být tak použita).
-   Vyžadované EAV entity - eav - object|EntityFormData[]
    -   Název entity - entity_name - string
    -   Popis entity - entity_description - string
    -   Atributy - attributes - object|AttributeData[]
        -   Název atributu - name - string
        -   Datový typ - date_type - enum(EAV\DataType)|string
        -   Popis atributu - description - string
        -   Placeholder - placeholder - string
        -   Generovaná hodnota - generate_value - enum(AttributeData::GENERATED_VALUES)|string
        -   Přednastavená hodnota - preset_value - string
        -   Je atribut povinný? - required - bool



### Jednotlivé stránky Latte:

Každá šablona může mít své registrované stránky, které pak při vytváření stránek může použít správce webu jako předlohu (např. Hlavní stránka/Kontaktní stránka/Běžná stránka či jinak definované.). Zároveň však může obsahovat také neregistrované stránky (komponenty), které umožňují neopakovat se a používat užitečné komponenty v rámci šablony. (Např. komponenta pro navigaci/hlavičku, komponenta pro patičku či nějaký souvislý blok)



**Dědičnost stránek Latte:**

Šablonovací systém FjordCMS umožňuje krom přednastaveného API také využívat běžné funkce dedičnosti v latte, abychom mohli nastavovat layout projektu či jednotlivých částí a nebo při práci se šablonou používat komponentové myšlení (tzn. rozdělit web na komponenty a upravovat pak jednotlivé části ve svých vlastních souborech).

Povolené makra dedičnosti: (viz [https://latte.nette.org/cs/template-inheritance#toc-jednotkova-dedicnost](https://latte.nette.org/cs/template-inheritance#toc-jednotkova-dedicnost))

-   embed
-   layout
-   import
-   block
-   define

**Vlastnosti registrovaných stránek Latte:**

**Parametry**

-   Název stránky (např. Hlavní stránka/Kontaktní stránka/Běžná stránka či jinak definované.) - title - string
-   Popis stránky (vč. využití) - description - string
-   Výstup - output - object
-   Content (s ohledem na typ výstupu) - content - string
-   Typ výstupu - type - enum("SRC", "PATH")/string