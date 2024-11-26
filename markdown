# Wireframe MediaBox pour Figma

## Structure des Frames

### Desktop (1440x900)
```figma
Frame "Desktop"
├── Header (1440x80)
│   ├── Logo (48x48)
│   ├── Title "MediaBox" (center)
│   └── Theme Toggle (right)
├── Navigation (1440x60)
│   ├── Home Icon
│   ├── Profile
│   ├── Audio
│   ├── Video
│   ├── Photo
│   ├── Text
│   └── Logout (right)
├── Search (600x40, centered)
└── Main Content (1440x720)
    └── Card Grid (4x1)
        ├── Audio Card (280x280)
        ├── Video Card (280x280)
        ├── Photo Card (280x280)
        └── Text Card (280x280)

### Tablet (768x1024)
Frame "Tablet"
├── Header (768x80)
├── Navigation (768x60)
│   └── Hamburger Menu
├── Search (500x40)
└── Main Content (768x844)
    └── Card Grid (2x2)

### Mobile (375x812)
Frame "Mobile"
├── Header (375x60)
├── Navigation (375x50)
│   └── Hamburger Menu
├── Search (340x40)
└── Main Content (375x662)
    └── Card Grid (1x4)

## Components

### Card Component
- Size: 280x280px (desktop)
- Border Radius: 70px 6%
- Shadow: var(--cardShadow)
- Content:
  - Icon (Material Icons, 150px)
  - Title (bottom)
  - Hover State

### Search Component
- Width: 600px (desktop)
- Height: 40px
- Border Radius: 20px
- Elements:
  - Input Field
  - Search Icon
  - Clear Button (optional)

### Navigation Component
- Height: 60px
- Background: Gradient
- Elements:
  - Icons + Labels
  - Active State
  - Hover State

## Color Styles

### Light Theme
- Background: #F5F5F5
- Text: #182020
- Primary: #2f4f4f
- Secondary: #000080
- Interactive: #747492

### Dark Theme
- Background: #464242
- Text: #F5F5F5
- Primary: #747492
- Secondary: #ffb764
- Interactive: #bd2121

## Typography

### Text Styles
- H1: Noto Sans Display / 5rem / Bold
- H2: Noto Sans Display / 3rem / Bold
- H3: Noto Sans Display / 1.5rem / Medium
- Body: Noto Sans Display / 16px / Regular

### Icons
- Material Icons
- Sizes: 48px, 36px, 24px, 16px

## Effects

### Shadows
- Cards: var(--cardShadow)
- Header: 2px 2px 10px var(--sombre)
- Buttons: inset 0 -5px 25px rgba(0,0,0,0.3)

### Interactions
- Hover States
- Active States
- Focus States
- Transitions (0.2s - 0.3s)

## Responsive Breakpoints
- Desktop: 1000px+
- Tablet: 601px - 999px
- Mobile: < 600px
```
