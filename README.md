# Easytools Subscription Manager

[![WordPress Plugin Version](https://img.shields.io/badge/WordPress%20Plugin-v1.5.5-blue.svg)](https://github.com/yourusername/easytools-subscription-manager)
[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)](https://www.php.net/)
[![WordPress Version](https://img.shields.io/badge/WordPress-5.0%2B-21759b.svg)](https://wordpress.org/)
[![License](https://img.shields.io/badge/License-GPL%20v2-green.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

Kompletny system zarządzania subskrypcjami dla WordPress zintegrowany z platformą płatniczą Easytools. Plugin automatycznie tworzy konta użytkowników, chroni premium treści, wysyła spersonalizowane e-maile powitalne i zarządza dostępem do subskrypcji.

[🇬🇧 English](#english-version) | [🇵🇱 Polish](#polish-version)

---

## 🇵🇱 Wersja Polska

### ✨ Główne Funkcjonalności

#### 🔄 Automatyczne Zarządzanie Subskrypcjami
- **Tworzenie kont użytkowników** - Automatyczne zakładanie kont WordPress po zakupie subskrypcji
- **Synchronizacja statusów** - Real-time aktualizacja statusu subskrypcji przez webhooks
- **Zarządzanie dostępem** - Automatyczna aktywacja/deaktywacja dostępu do treści premium

#### 🔐 Zaawansowana Kontrola Dostępu
- **Dwa tryby ochrony:**
  - Chroń całą stronę (z wyjątkami)
  - Chroń wybrane strony
- **Elastyczna konfiguracja** - Wybór dowolnych stron/postów do ochrony
- **Inteligentne przekierowania** - Automatyczne przekierowania dla użytkowników bez dostępu

#### 🎨 Bouncer Page - Piękna Strona Blokady
- **Generator w 1 klik** - Automatyczne tworzenie strony z szablonu
- **Pełna personalizacja:**
  - Kolor ikony kłódki
  - Kolor przycisku CTA
  - Kolor tła strony
  - Link do produktu Easytools
- **Kopiowanie HTML** - Możliwość skopiowania kodu do własnej edycji
- **Responsywny design** - Perfekcyjne wyświetlanie na wszystkich urządzeniach

#### 📧 Spersonalizowane E-maile Powitalne
- **Piękny design HTML** - Profesjonalnie zaprojektowane szablony
- **Pełna personalizacja:**
  - Kolory brandingowe
  - Temat wiadomości
  - Nagłówek
  - Treść wiadomości
  - Tekst przycisku CTA
- **Zmienne dynamiczne:** `{username}`, `{site_name}`, `{login_url}`
- **Test wysyłki** - Funkcja testowego wysłania e-maila
- **Reset hasła** - Automatyczny link do ustawienia hasła

#### 🔗 Integracja Webhooks
- **Bezpieczna weryfikacja** - Kryptograficzna weryfikacja podpisu webhook
- **Obsługiwane zdarzenia:**
  - `subscription.active` - Aktywacja subskrypcji
  - `subscription.expired` - Wygaśnięcie subskrypcji
  - `subscription.cancelled` - Anulowanie subskrypcji
- **Logi webhook** - Szczegółowe logowanie wszystkich zdarzeń
- **Tester webhook** - Wbudowane narzędzie do testowania połączenia

#### 👥 Panel Zarządzania Subskrybentami
- **Lista wszystkich użytkowników** - Przejrzysty widok statusów subskrypcji
- **Statusy wizualne:**
  - 🟢 Aktywna subskrypcja
  - 🔴 Wygasła subskrypcja
- **Manualna kontrola** - Możliwość ręcznej aktywacji/deaktywacji dostępu
- **Informacje o subskrypcji:**
  - Typ subskrypcji (monthly, annual, itp.)
  - Data rozpoczęcia
  - Data zakończenia
  - Email użytkownika

#### 📊 Monitoring i Logi
- **Szczegółowe logi webhook** - Każde zdarzenie jest rejestrowane
- **Eksport danych:**
  - Format CSV (dla arkuszy kalkulacyjnych)
  - Format Markdown (dla dokumentacji)
- **Filtry czasowe** - Eksport logów z wybranego zakresu dat
- **Status działań** - Natychmiastowa informacja o sukcesie/błędzie

### 🚀 Instalacja

#### Wymagania
- WordPress 5.0 lub nowszy
- PHP 7.4 lub nowszy
- Certyfikat SSL (HTTPS)
- Aktywne konto Easytools

#### Krok 1: Pobierz Plugin
Pobierz najnowszą wersję: [`easytools-subscription-manager-v1.5.5.zip`](https://github.com/yourusername/easytools-subscription-manager/releases/latest)

#### Krok 2: Zainstaluj w WordPress
1. Zaloguj się do panelu WordPress
2. Przejdź do **Wtyczki → Dodaj nową → Wyślij wtyczkę na serwer**
3. Wybierz pobrany plik ZIP
4. Kliknij **Zainstaluj**
5. Kliknij **Aktywuj wtyczkę**

#### Krok 3: Konfiguracja Podstawowa

**A. Checkout URL**
1. W Easytools utwórz produkt subskrypcyjny
2. Skopiuj URL checkout (np. `https://easl.ink/twojprodukt`)
3. Wklej w polu **Checkout URL**

**B. Webhook Signing Key**
1. W Easytools: **API & Webhooks → Generuj Webhook Signing Key**
2. Skopiuj wygenerowany klucz
3. W WordPress: kliknij ikonę oka obok pola **Webhook Signing Key**
4. Wklej skopiowany klucz
5. **Skopiuj URL webhook** pokazany poniżej pola

**C. Dodaj Webhook w Easytools**
1. W Easytools: **API & Webhooks → Dodaj nowy webhook**
2. Wklej URL webhook z WordPress
3. Zaznacz zdarzenia:
   - ✅ subscription.active
   - ✅ subscription.expired
   - ✅ subscription.cancelled
4. Zapisz webhook

**D. Zapisz ustawienia**
Kliknij **Zapisz ustawienia** w WordPress.

### 📖 Konfiguracja Funkcji

#### Kontrola Dostępu

**Tryb 1: Chroń Wybrane Strony**
```
1. W sekcji "Kontrola Dostępu"
2. NIE zaznaczaj "Chroń całą stronę"
3. W "Chronione strony" zaznacz strony premium (Ctrl+klik dla wielu)
4. Zapisz ustawienia
```

**Tryb 2: Chroń Całą Stronę (z wyjątkami)**
```
1. Zaznacz "Chroń całą stronę"
2. W "Wykluczone strony" zaznacz strony publiczne (Start, O nas, Kontakt)
3. Zapisz ustawienia
```

#### Bouncer Page

**Tworzenie strony blokady:**
1. Przewiń do sekcji **Bouncer Page**
2. Zaznacz **Włącz niestandardową stronę bouncer**
3. Wprowadź **URL produktu** (link do Easytools)
4. Dostosuj kolory:
   - Kolor ikony (domyślnie: #71efab)
   - Kolor przycisku (domyślnie: #71efab)
   - Kolor tła (domyślnie: #172532)
5. Kliknij **Utwórz nową stronę Bouncer z szablonu**
6. Poczekaj na komunikat sukcesu ✅
7. Zapisz ustawienia

**Funkcje:**
- ✅ Automatyczne wypełnianie URL z pola Checkout URL
- ✅ Walidacja - nie pozwoli utworzyć strony bez URL produktu
- ✅ Podświetlenie pola na zielono przy auto-synchronizacji
- ✅ Możliwość ręcznej zmiany URL (wyłącza auto-sync)

#### E-maile Powitalne

**Podstawowa konfiguracja:**
```
1. Sekcja "Rejestracja użytkownika"
2. Zaznacz "Wysyłaj e-mail powitalny"
3. Ustaw adres e-mail nadawcy
4. Ustaw nazwę nadawcy
```

**Personalizacja wyglądu:**
```
1. Sekcja "Dostosowanie treści e-maila"
2. Wybierz kolor brandingowy
3. Edytuj temat, nagłówek, treść
4. Użyj zmiennych: {username}, {site_name}, {login_url}
5. Wyślij testowy e-mail do siebie
6. Zapisz ustawienia
```

### 🧪 Testowanie

#### Test 1: Połączenie Webhook
```
Easytools Subscription → Webhook Tester
→ Wprowadź testowy e-mail
→ Kliknij "Wyślij testowy webhook"
→ Sprawdź zakładkę "Logi" - powinien być wpis z ✅
```

#### Test 2: Tworzenie Konta
```
1. Wykonaj testowy zakup w Easytools
2. Sprawdź zakładkę "Subskrybenci" - powinien być nowy użytkownik
3. Sprawdź e-mail - powinien być e-mail powitalny
```

#### Test 3: Kontrola Dostępu
```
1. Otwórz okno incognito
2. Spróbuj wejść na chronioną stronę
3. Powinieneś być przekierowany na Bouncer Page (lub checkout)
```

#### Test 4: Dostęp Subskrybenta
```
1. Zaloguj się jako subskrybent
2. Wejdź na chronioną stronę
3. Powinieneś mieć pełny dostęp ✅
```

### 🛠️ API i Hooki

#### Sprawdzanie statusu subskrypcji
```php
if (Easytools_Access_Control::has_active_subscription()) {
    // Użytkownik ma aktywną subskrypcję
    echo 'Witaj, subskrybencie premium!';
}
```

#### Pobieranie URL checkout z e-mailem
```php
$checkout_url = Easytools_Access_Control::get_checkout_url_with_email('user@example.com');
```

### 📋 Rozwiązywanie Problemów

#### Problem: E-maile nie są wysyłane

**Rozwiązanie:**
1. Zainstaluj wtyczkę "WP Mail SMTP"
2. Skonfiguruj SMTP (Gmail, SendGrid, itp.)
3. Sprawdź folder spam
4. Upewnij się, że "Wysyłaj e-mail powitalny" jest zaznaczone

#### Problem: Konto użytkownika nie zostało utworzone

**Rozwiązanie:**
1. Sprawdź logi webhook (zakładka "Logi")
2. Zweryfikuj Webhook Signing Key
3. Upewnij się, że URL webhook jest poprawny (HTTPS!)
4. Odśwież permalinki: **Ustawienia → Bezpośrednie odnośniki → Zapisz**

#### Problem: Chronione strony nadal dostępne

**Rozwiązanie:**
1. Sprawdź ustawienia kontroli dostępu
2. Wyczyść cache (jeśli używasz wtyczki cache)
3. Testuj w trybie incognito
4. Upewnij się, że Bouncer Page nie jest chroniona

#### Problem: Link resetowania hasła pokazuje "Invalid key"

**Rozwiązanie:**
Zaktualizuj plugin do wersji 1.5.3 lub nowszej - problem został naprawiony.

### 🔒 Bezpieczeństwo

- ✅ Weryfikacja podpisu webhook (kryptograficzna)
- ✅ Autentykacja API token
- ✅ WordPress nonces dla AJAX
- ✅ Sanitizacja i walidacja wszystkich danych wejściowych
- ✅ Prepared statements dla zapytań SQL
- ✅ Escape output dla XSS protection

### 🌐 Wielojęzyczność

Plugin jest gotowy do tłumaczenia i zawiera:
- 🇵🇱 **Polski** - pełne tłumaczenie (dołączone)
- 🇬🇧 **Angielski** - język bazowy
- Możliwość dodania własnych tłumaczeń przez pliki `.po/.mo`

### 📊 Struktura Plików

```
easytools-subscription-manager/
├── easytools-subscription-manager.php  # Główny plik wtyczki
├── includes/
│   ├── class-access-control.php        # Kontrola dostępu
│   ├── class-admin-settings.php        # Panel administracyjny
│   ├── class-email-handler.php         # Wysyłka e-maili
│   ├── class-webhook-handler.php       # Obsługa webhook
│   ├── class-webhook-logger.php        # Logowanie webhook
│   ├── class-webhook-tester.php        # Tester webhook
│   ├── class-dashboard-widget.php      # Widget dashboardu
│   ├── class-user-functions.php        # Funkcje użytkownika
│   └── class-shortcodes.php            # Shortcodes
├── assets/
│   └── css/
│       └── admin-premium.css           # Style premium admin
└── languages/
    ├── easytools-sub-pl_PL.po          # Tłumaczenie PL
    └── easytools-sub-pl_PL.mo          # Skompilowane PL
```

### 🔄 Historia Wersji

#### v1.5.5 (Aktualna)
- ✅ Automatyczna synchronizacja Checkout URL z Product URL
- ✅ Inteligentne wypełnianie pól (nie wymaga podwójnego wprowadzania)
- ✅ Wizualne potwierdzenie synchronizacji (zielone podświetlenie)
- ✅ Możliwość ręcznego override URL

#### v1.5.4
- ✅ Walidacja URL produktu przed utworzeniem Bouncer Page
- ✅ Wymagane pole z wizualnym wskaźnikiem
- ✅ Automatyczne podświetlenie pustego pola na czerwono

#### v1.5.3
- ✅ Usunięcie komentarzy HTML (fix odstępów w Bouncer Page)
- ✅ Dodana stopka z autorem w panelu ustawień
- ✅ Fix formatowania Bouncer Page

#### v1.5.2
- ✅ Fix pionowego wyrównania tekstu na przycisku
- ✅ Usunięcie dodatkowych białych znaków

#### v1.5.1
- ✅ Komunikaty potwierdzenia przy tworzeniu Bouncer Page
- ✅ Fix aktualizacji kolorów w czasie rzeczywistym
- ✅ Zmiana domyślnej nazwy strony na "Bouncer Page"

#### v1.5.0
- ✅ Kompletny system Bouncer Page
- ✅ Dostosowywalne kolory (ikona, przycisk, tło)
- ✅ Generator strony w 1 klik
- ✅ Funkcja kopiowania HTML

#### v1.4.x - v1.3.x
Zobacz pełną historię w [CHANGELOG.md](CHANGELOG.md)

### 💡 Najlepsze Praktyki

#### Wysyłka E-maili
- Używaj profesjonalnej usługi SMTP (SendGrid, Mailgun)
- Skonfiguruj rekordy SPF/DKIM dla domeny
- Regularnie testuj dostarczalność na różnych providerach

#### Kontrola Dostępu
- "Chroń całą stronę" dla stron członkowskich
- "Chroń wybrane strony" dla mieszanej treści
- Zawsze pozostaw strony prawne (Regulamin, Prywatność) niezabezpieczone

#### Bouncer Page
- Dopasuj kolory do brandingu
- Testuj na urządzeniach mobilnych
- A/B testuj różne call-to-action

#### Monitoring
- Sprawdzaj logi webhook co tydzień
- Eksportuj logi co miesiąc dla rekordów
- Monitoruj wskaźnik błędów

### 🤝 Wsparcie

**Dokumentacja:**
- [Przewodnik użytkownika](PLUGIN-GUIDE.md)
- [Skrypt wideo](VIDEO-SCRIPT.md)
- [Szybki start](QUICK-START-GUIDE.md)

**Kontakt:**
- **Email:** kontakt.rapacz@gmail.com
- **LinkedIn:** [Krzysztof Rapacz](https://www.linkedin.com/in/krzysztofrapacz/)

**Platforma Easytools:**
- [Easytools.pl](https://easy.tools)
- [Dokumentacja EN](https://www.easy.tools/docs/explore)
- [Dokumentacja PL](https://www.easy.tools/pl/docs/odkrywaj)

### 📄 Licencja

Ten plugin jest udostępniany na licencji **GPL v2 lub nowszej**.

```
Copyright (C) 2024 Chris Rapacz

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
```

### 👨‍💻 Autor

**Chris Rapacz (Krzysztof Rapacz)**

Deweloper WordPress i specjalista integracji systemów płatniczych.

- 🌐 Website: [chrisrapacz.com](https://www.chrisrapacz.com)
- 💼 LinkedIn: [linkedin.com/in/krzysztofrapacz](https://www.linkedin.com/in/krzysztofrapacz/)
- 📧 Email: kontakt.rapacz@gmail.com

### 🙏 Podziękowania

- Zespół WordPress za świetną platformę
- Easytools za elastyczną platformę płatniczą
- Społeczność open-source za inspirację

---

## 🇬🇧 English Version

### ✨ Key Features

#### 🔄 Automatic Subscription Management
- **User Account Creation** - Automatically create WordPress accounts after subscription purchase
- **Status Synchronization** - Real-time subscription status updates via webhooks
- **Access Management** - Automatic activation/deactivation of premium content access

#### 🔐 Advanced Access Control
- **Two Protection Modes:**
  - Protect entire site (with exceptions)
  - Protect specific pages
- **Flexible Configuration** - Choose any pages/posts to protect
- **Smart Redirects** - Automatic redirects for users without access

#### 🎨 Bouncer Page - Beautiful Block Page
- **1-Click Generator** - Automatically create page from template
- **Full Customization:**
  - Lock icon color
  - CTA button color
  - Page background color
  - Easytools product link
- **HTML Copy** - Copy code for manual editing
- **Responsive Design** - Perfect display on all devices

#### 📧 Personalized Welcome Emails
- **Beautiful HTML Design** - Professionally designed templates
- **Full Personalization:**
  - Brand colors
  - Email subject
  - Heading
  - Message content
  - CTA button text
- **Dynamic Variables:** `{username}`, `{site_name}`, `{login_url}`
- **Test Send** - Test email sending function
- **Password Reset** - Automatic password setup link

#### 🔗 Webhook Integration
- **Secure Verification** - Cryptographic webhook signature verification
- **Supported Events:**
  - `subscription.active` - Subscription activation
  - `subscription.expired` - Subscription expiration
  - `subscription.cancelled` - Subscription cancellation
- **Webhook Logs** - Detailed logging of all events
- **Webhook Tester** - Built-in connection testing tool

#### 👥 Subscriber Management Panel
- **All Users List** - Clear view of subscription statuses
- **Visual Statuses:**
  - 🟢 Active subscription
  - 🔴 Expired subscription
- **Manual Control** - Manual activation/deactivation of access
- **Subscription Information:**
  - Subscription type (monthly, annual, etc.)
  - Start date
  - End date
  - User email

#### 📊 Monitoring and Logs
- **Detailed Webhook Logs** - Every event is recorded
- **Data Export:**
  - CSV format (for spreadsheets)
  - Markdown format (for documentation)
- **Time Filters** - Export logs from selected date range
- **Action Status** - Immediate success/error information

### 🚀 Installation

See Polish version above for detailed installation and configuration instructions.

### 📖 Documentation

- [Complete Plugin Guide](PLUGIN-GUIDE.md)
- [Video Script](VIDEO-SCRIPT.md)
- [Quick Start Guide](QUICK-START-GUIDE.md)

### 👨‍💻 Author

**Chris Rapacz (Krzysztof Rapacz)**

WordPress Developer and Payment Integration Specialist.

- 🌐 Website: [chrisrapacz.com](https://www.chrisrapacz.com)
- 💼 LinkedIn: [linkedin.com/in/krzysztofrapacz](https://www.linkedin.com/in/krzysztofrapacz/)
- 📧 Email: kontakt.rapacz@gmail.com

### 📄 License

GPL v2 or later

---

**Made with ❤️ in Poland**

*Easytools Subscription Manager - Professional subscription management for WordPress*
