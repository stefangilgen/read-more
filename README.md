# Read More Ajax

Ein WordPress Plugin, das "Weiterlesen"-Links per AJAX nachladen lässt, ohne die Seite neu zu laden.

## Features
- Lädt Beitragsinhalte dynamisch nach
- Smooth Animations beim Ein-/Ausblenden
- Fallback wenn JavaScript deaktiviert ist
- Unterstützt den WordPress Block Editor und Classic Editor
- Leichtgewichtig und performant

## Installation
1. Laden Sie den Plugin-Ordner in das `/wp-content/plugins/` Verzeichnis hoch
2. Aktivieren Sie das Plugin im WordPress Admin-Bereich
3. Verwenden Sie den "Mehr"-Block (<!--more-->) in Ihren Beiträgen

## Verwendung
Fügen Sie einfach einen "Mehr"-Block in Ihren Beitrag ein:
- **Block Editor**: Fügen Sie den "Mehr"-Block an der gewünschten Stelle ein
- **Classic Editor**: Klicken Sie auf den "Weiterlesen"-Button in der Editor-Toolbar

## Events
Das Plugin triggert folgende Events:
- `readmore:loaded`: Wird ausgelöst, wenn neuer Content geladen wurde

## Entwickler
- Unterstützt Theme-Anpassungen via CSS
- Voll kompatibel mit Custom Post Types
- Hooks für Entwickler verfügbar

## Mindestanforderungen
- WordPress 5.0 oder höher
- PHP 7.0 oder höher
- JavaScript aktiviert im Browser

## Lizenz
GPLv2 oder später 