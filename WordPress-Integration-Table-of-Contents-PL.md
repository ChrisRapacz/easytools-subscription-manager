# Jak połączyć WordPress z Easytools – Spis treści

## Wprowadzenie
- Czego nauczysz się w tym tutorialu
- Wyjaśnienie dwóch metod integracji:
  - Metoda 1: Webhooks z pluginem Easytools Subscription Manager
  - Metoda 2: Natywne automatyzacje Easytools

---

## Część 1: Zrozumienie webhooków w Easytools

### 1.1 Czym są webhooks?
- Definicja i jak działają
- Komunikacja w czasie rzeczywistym między Easytools a WordPressem
- Dlaczego webhooks są ważne dla zarządzania subskrypcjami
- Konfiguracja webhooków w Easytools
- Jaki jest format URL twojego webhooka

### 1.2 Eventy webhooków Easytools
- Wyjaśnienie dostępnych eventów z krótkim spojrzeniem na strukturę JSON payload

### 1.3 Bezpieczeństwo webhooków z HMAC
- Czym jest podpisywanie webhooków
- Dlaczego musisz weryfikować webhooks
- Generowanie Webhook Signing Key w Easytools
- Ważne: Bezpieczne zapisanie klucza (nie zobaczysz go ponownie!)

---

## Część 2: Konfiguracja WordPressa

### 2.1 Instalacja pluginu Easytools Subscription Manager
- Informacja, że to jedno z możliwych rozwiązań
- Pobieranie pluginu
- Instalacja przez panel administracyjny WordPressa
- Aktywacja pluginu
- Pierwsze spojrzenie na interfejs pluginu

### 2.2 Zrozumienie ról użytkowników WordPress
- Domyślne role WordPress (Subscriber, Contributor, Author, Editor, Administrator)
- Którą rolę przypisać płatnym subskrybentom (sugestia, żeby było to Subscriber)

---

## Część 3: Konfiguracja pluginu Easytools Subscription Manager

### 3.1 Dostęp do ustawień pluginu
- Gdzie znaleźć plugin w panelu administracyjnym WordPress
- Przegląd wszystkich zakładek pluginu

### 3.2 Konfiguracja podstawowych ustawień
- Ustawienie Webhook Signing Key (z Easytools)
- Konfiguracja Checkout URL (URL twojego sklepu Easytools)
- Ochrona całej strony vs. konkretnych stron


---

## Część 4: Testowanie integracji webhooków

### 4.1 Korzystanie z wbudowanego testera webhooków
- Dostęp do zakładki "Webhook Testing" w pluginie
- Zrozumienie przykładowych payloadów
- Wybór eventu testowego (np. Product Assigned)
- Edycja danych payload (opcjonalnie)
- Wysyłanie testowego webhooka
- Odczytywanie odpowiedzi

### 4.2 Sprawdzanie logów webhooków
- Dostęp do zakładki "Webhook Logs"
- Zrozumienie wpisów w logach:
  - Znacznik czasu
  - Typ eventu
  - Status (sukces/porażka)
  - Pełny payload
  - Odpowiedź
- Inspekcja szczegółów pojedynczych webhooków
- Używanie logów do debugowania

### 4.3 Kalkulator HMAC
- Do czego służy kalkulator HMAC
- Kiedy go używać (testowanie z Postmanem, zewnętrznymi narzędziami)
- Krok po kroku:
  - Wklejanie payloadu
  - Wprowadzanie klucza podpisującego
  - Generowanie podpisu
  - Kopiowanie podpisu do użycia w nagłówkach

### 4.4 Testowanie z Postmanem (Zaawansowane)
- Konfiguracja żądania w Postmanie
- Metoda: POST
- URL: Twój endpoint webhooka
- Nagłówki:
  - `Content-Type: application/json`
  - `x-webhook-signature: [wygenerowany podpis]`
- Body: Raw JSON payload
- Wysyłanie i weryfikacja odpowiedzi

---

## Część 5: Konfiguracja natywnych automatyzacji Easytools

### 5.0 Generowanie hasła aplikacji w WordPressie
- Dlaczego hasła aplikacji są potrzebne
- Krok po kroku: Jak wygenerować hasło aplikacji
- Gdzie je znaleźć w profilu użytkownika WordPress
- Najlepsze praktyki bezpieczeństwa
- Jak cofnąć dostęp w razie potrzeby

### 5.1 Kiedy używać automatyzacji a kiedy webhooków
- Czy można używać obu? Tak! Jak się uzupełniają

### 5.2 Dodawanie aplikacji WordPress w Easytools
- Przejście do Automations → Applications
- Kliknięcie "+ New application"
- Wybór WordPress z listy
- Nazwanie integracji

### 5.3 Łączenie WordPressa z Easytools
- Wprowadzanie URL twojej strony WordPress
- Używanie nazwy użytkownika aplikacji (twoja nazwa administratora WordPress)
- Wklejanie hasła aplikacji (wygenerowanego w części 6.0)
- Kliknięcie "Check connection"
- Weryfikacja pomyślnego połączenia
- Zapisywanie aplikacji

### 5.4 Tworzenie scenariuszy automatyzacji
- Przejście do zakładki Scenarios
- Kliknięcie "+ New scenario"
- Wybór eventu wyzwalającego (np. "Order Completed")
- Wybór aplikacji WordPress
- Wybór akcji: "Create User"
- Konfiguracja roli użytkownika (Subscriber, Author, itp.)
- Przypisywanie do produktów/wariantów

### 5.5 Wykonywanie i monitorowanie scenariuszy
- Gdzie znaleźć zakładkę Executions
- Czytanie logów wykonania
- Zrozumienie statusu sukcesu vs. porażki
- Ponowne próby nieudanych wykonań
- Anulowanie oczekujących wykonań

---

## Część 6: Testowanie kompletnego przepływu

### 6.1 Tworzenie testowego subskrybenta
- Konfiguracja produktu testowego w Easytools
- Tworzenie konta testowego użytkownika w WordPressie
- Zapewnienie, że adresy email się zgadzają

### 6.2 Ochrona treści w WordPressie
- Tworzenie strony testowej z treściami premium
- Używanie pluginu do ograniczenia dostępu do tej strony (Easytools Subscription Manager lub innego, jeśli pierwszy nie jest zainstalowany - skupię się na tym o nazwie Simple Restrict)

### 6.3 Testowanie przepływu przekierowania
- Wylogowanie się z WordPressa (lub używanie trybu incognito)
- Próba dostępu do chronionej strony
- Weryfikacja przekierowania do checkoutu Easytools
- Co użytkownik widzi na każdym etapie
- Konfiguracja strony "Bouncer" do kierowania subskrybentów, którzy nie są zalogowani i nowych użytkowników do checkoutu

### 6.4 Dokończenie testowego zakupu
- Wypełnianie formularza checkoutu
- Finalizacja płatności

### 6.5 Weryfikacja dostarczenia webhooka
- Oczekiwany czas: 5-10 sekund
- Sprawdzanie logów webhooków w pluginie
- Weryfikacja, że użytkownik został zaktualizowany w bazie danych WordPress
- Sprawdzanie statusu subskrypcji użytkownika

### 6.6 Testowanie odblokowania dostępu
- Powrót na stronę WordPress
- Logowanie (jeśli potrzebne)
- Dostęp do wcześniej chronionej strony
- Weryfikacja, że treść jest teraz widoczna

### 6.7 Testowanie wygaśnięcia subskrypcji
- Manualne wyzwolenie webhooka wygaśnięcia (tryb testowy)
- Weryfikacja, że dostęp został cofnięty
- Testowanie przekierowania z powrotem do checkoutu
