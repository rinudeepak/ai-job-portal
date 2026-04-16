# HireMatrix Responsive Visual Guide

## 📱 Responsive Behavior by Component

### Navigation Bar

```
┌─────────────────────────────────────────────────────────────┐
│ Desktop (≥ 1200px)                                          │
├─────────────────────────────────────────────────────────────┤
│ [Logo] [Jobs ▼] [Companies] [Services ▼]  [🔍] [🔔] [👤]  │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│ Tablet (768px - 991px)                                      │
├─────────────────────────────────────────────────────────────┤
│ [Logo]                              [🔍] [🔔] [☰]          │
└─────────────────────────────────────────────────────────────┘

┌──────────────────────────────────┐
│ Mobile (< 768px)                 │
├──────────────────────────────────┤
│ [Logo]         [🔍] [🔔] [☰]    │
└──────────────────────────────────┘
```

### Job Listings Grid

```
Desktop (≥ 1200px):
┌──────────┬──────────┬──────────┐
│  Job 1   │  Job 2   │  Job 3   │
├──────────┼──────────┼──────────┤
│  Job 4   │  Job 5   │  Job 6   │
└──────────┴──────────┴──────────┘

Tablet (768px - 991px):
┌──────────┬──────────┐
│  Job 1   │  Job 2   │
├──────────┼──────────┤
│  Job 3   │  Job 4   │
├──────────┼──────────┤
│  Job 5   │  Job 6   │
└──────────┴──────────┘

Mobile (< 768px):
┌──────────┐
│  Job 1   │
├──────────┤
│  Job 2   │
├──────────┤
│  Job 3   │
├──────────┤
│  Job 4   │
└──────────┘
```

### Dashboard Layout

```
Desktop (≥ 1200px):
┌────────┬─────────────────────────────┐
│        │                             │
│ Side   │     Main Content            │
│ bar    │                             │
│        │                             │
└────────┴─────────────────────────────┘

Mobile (< 768px):
┌─────────────────────────────────┐
│         Sidebar                 │
├─────────────────────────────────┤
│                                 │
│       Main Content              │
│                                 │
└─────────────────────────────────┘
```

### Tables

```
Desktop (≥ 992px):
┌────────────────────────────────────────────────────────┐
│ Title    │ Company  │ Location │ Salary  │ Actions   │
├──────────┼──────────┼──────────┼─────────┼───────────┤
│ Dev      │ Tech Co  │ SF       │ $120K   │ [View]    │
└────────────────────────────────────────────────────────┘

Mobile (< 992px) - Horizontal Scroll:
┌──────────────────────────────────────────────────────────────►
│ Title    │ Company  │ Location │ Salary  │ Actions   │
├──────────┼──────────┼──────────┼─────────┼───────────┤
│ Dev      │ Tech Co  │ SF       │ $120K   │ [View]    │
└──────────────────────────────────────────────────────────────►
        ◄─── Swipe to scroll ───►
```

### Forms

```
Desktop (≥ 768px):
┌─────────────────────────────────────────┐
│ [Name Input]        [Email Input]       │
│ [Phone Input]       [Location Input]    │
│ [Message Textarea                    ]  │
│                                          │
│ [Submit Button] [Cancel Button]         │
└─────────────────────────────────────────┘

Mobile (< 768px):
┌─────────────────────────────────┐
│ [Name Input                  ]  │
│ [Email Input                 ]  │
│ [Phone Input                 ]  │
│ [Location Input              ]  │
│ [Message Textarea            ]  │
│                                 │
│ [Submit Button               ]  │
│ [Cancel Button               ]  │
└─────────────────────────────────┘
```

### Cards

```
Desktop (≥ 992px):
┌──────────┬──────────┬──────────┬──────────┐
│ Card 1   │ Card 2   │ Card 3   │ Card 4   │
└──────────┴──────────┴──────────┴──────────┘

Tablet (768px - 991px):
┌──────────┬──────────┐
│ Card 1   │ Card 2   │
├──────────┼──────────┤
│ Card 3   │ Card 4   │
└──────────┴──────────┘

Mobile (< 768px):
┌──────────┐
│ Card 1   │
├──────────┤
│ Card 2   │
├──────────┤
│ Card 3   │
├──────────┤
│ Card 4   │
└──────────┘
```

### Modals

```
Desktop (≥ 768px):
┌─────────────────────────────────────────┐
│                                         │
│    ┌─────────────────────────┐         │
│    │  Modal Title        [×] │         │
│    ├─────────────────────────┤         │
│    │                         │         │
│    │  Modal Content          │         │
│    │                         │         │
│    ├─────────────────────────┤         │
│    │  [Cancel]  [Confirm]    │         │
│    └─────────────────────────┘         │
│                                         │
└─────────────────────────────────────────┘

Mobile (< 768px):
┌─────────────────────────────────┐
│  Modal Title              [×]   │
├─────────────────────────────────┤
│                                 │
│  Modal Content                  │
│                                 │
├─────────────────────────────────┤
│  [Cancel Button              ]  │
│  [Confirm Button             ]  │
└─────────────────────────────────┘
```

## 🎯 Touch Target Sizes

### Minimum Touch Targets (44px × 44px)

```
✅ Good (44px+):
┌──────────────┐
│              │
│    Button    │
│              │
└──────────────┘

❌ Bad (< 44px):
┌────────┐
│ Button │
└────────┘
```

### Spacing Between Touch Targets

```
✅ Good (8px+ spacing):
┌──────┐  ┌──────┐  ┌──────┐
│ Btn1 │  │ Btn2 │  │ Btn3 │
└──────┘  └──────┘  └──────┘
   ↔8px↔     ↔8px↔

❌ Bad (no spacing):
┌──────┬──────┬──────┐
│ Btn1 │ Btn2 │ Btn3 │
└──────┴──────┴──────┘
```

## 📏 Typography Scaling

```
Desktop (≥ 1200px):
┌─────────────────────────────────────────┐
│ Heading 1 (2.5rem / 40px)               │
│ Heading 2 (2rem / 32px)                 │
│ Heading 3 (1.75rem / 28px)              │
│ Body Text (1rem / 16px)                 │
└─────────────────────────────────────────┘

Tablet (768px - 991px):
┌─────────────────────────────────┐
│ Heading 1 (2rem / 32px)         │
│ Heading 2 (1.75rem / 28px)      │
│ Heading 3 (1.5rem / 24px)       │
│ Body Text (1rem / 16px)         │
└─────────────────────────────────┘

Mobile (< 768px):
┌─────────────────────────────┐
│ Heading 1 (1.75rem / 28px)  │
│ Heading 2 (1.5rem / 24px)   │
│ Heading 3 (1.25rem / 20px)  │
│ Body Text (1rem / 16px)     │
└─────────────────────────────┘
```

## 🔄 Horizontal Scroll Behavior

### Normal Viewport (≥ 360px)
```
┌─────────────────────────────────┐
│                                 │
│  Content fits within viewport   │
│  No horizontal scroll           │
│                                 │
└─────────────────────────────────┘
```

### Small Viewport (< 360px) - Naukri Style
```
┌──────────────────────────────────────────────►
│                                 │
│  Content maintains 360px width  │
│  Horizontal scroll enabled      │
│                                 │
└──────────────────────────────────────────────►
        ◄─── Swipe to scroll ───►
```

## 📊 Breakpoint Transitions

```
┌─────────────────────────────────────────────────────────────┐
│                                                             │
│  xs        sm          md          lg          xl           │
│  ├──────────┼───────────┼───────────┼───────────┼──────►   │
│  0         480        768         992        1200          │
│                                                             │
│  Mobile    Phone     Tablet    Laptop     Desktop          │
│                                                             │
└─────────────────────────────────────────────────────────────┘

Transitions:
• 0-479px:    Mobile (1 column, stacked)
• 480-767px:  Phone (1-2 columns)
• 768-991px:  Tablet (2-3 columns)
• 992-1199px: Laptop (3-4 columns)
• 1200px+:    Desktop (4+ columns)
```

## 🎨 Container Padding

```
Desktop (≥ 1200px):
┌─────────────────────────────────────────┐
│ ←15px→                       ←15px→     │
│        Content Area                     │
│                                         │
└─────────────────────────────────────────┘

Tablet (768px - 991px):
┌─────────────────────────────────┐
│ ←15px→                 ←15px→   │
│      Content Area               │
│                                 │
└─────────────────────────────────┘

Mobile (< 768px):
┌─────────────────────────────┐
│ ←12px→           ←12px→     │
│    Content Area             │
│                             │
└─────────────────────────────┘
```

## 🖼️ Image Responsiveness

```
Desktop:
┌─────────────────────────────────────────┐
│                                         │
│         [Full Size Image]               │
│                                         │
└─────────────────────────────────────────┘

Mobile:
┌─────────────────────────────┐
│                             │
│  [Scaled Image]             │
│                             │
└─────────────────────────────┘

CSS:
img {
    max-width: 100%;
    height: auto;
}
```

## 🎯 Job Card Responsive Behavior

```
Desktop (≥ 1200px):
┌────────────────────────────────────────────────────────────┐
│ [Logo] Senior Developer                    [Apply Button]  │
│        Tech Corp • San Francisco, CA                       │
│        $120K - $150K • Full-time                          │
│        ████████████░░░░░░░░ 85% Match                     │
└────────────────────────────────────────────────────────────┘

Mobile (< 768px):
┌─────────────────────────────────┐
│ [Logo]                          │
│ Senior Developer                │
│ Tech Corp                       │
│ San Francisco, CA               │
│ $120K - $150K                   │
│ Full-time                       │
│ ████████████░░░░░░░░ 85% Match  │
│ [Apply Button                ]  │
└─────────────────────────────────┘
```

## 📱 Profile Page Layout

```
Desktop (≥ 992px):
┌────────┬─────────────────────────────┐
│        │ Name                        │
│ Photo  │ Title                       │
│        │ Location                    │
│        │ [Edit] [Share]              │
├────────┼─────────────────────────────┤
│ About  │ Experience                  │
│ Skills │ Education                   │
│ Links  │ Certifications              │
└────────┴─────────────────────────────┘

Mobile (< 992px):
┌─────────────────────────────────┐
│          [Photo]                │
│           Name                  │
│           Title                 │
│          Location               │
│    [Edit Button             ]   │
│    [Share Button            ]   │
├─────────────────────────────────┤
│ About                           │
├─────────────────────────────────┤
│ Skills                          │
├─────────────────────────────────┤
│ Experience                      │
├─────────────────────────────────┤
│ Education                       │
├─────────────────────────────────┤
│ Certifications                  │
└─────────────────────────────────┘
```

## 🎬 Animation & Transitions

```
Desktop Hover Effects:
┌──────────┐         ┌──────────┐
│  Card    │  hover  │  Card    │
│          │  ───►   │  ↑       │
│          │         │ (lifted) │
└──────────┘         └──────────┘

Mobile Touch Effects:
┌──────────┐         ┌──────────┐
│  Card    │  tap    │  Card    │
│          │  ───►   │ (pressed)│
│          │         │          │
└──────────┘         └──────────┘
```

## 📐 Grid System Examples

### 12-Column Grid

```
Desktop:
┌───┬───┬───┬───┬───┬───┬───┬───┬───┬───┬───┬───┐
│ 1 │ 2 │ 3 │ 4 │ 5 │ 6 │ 7 │ 8 │ 9 │10 │11 │12 │
└───┴───┴───┴───┴───┴───┴───┴───┴───┴───┴───┴───┘

col-md-6 + col-md-6:
┌─────────────────────┬─────────────────────┐
│        6 cols       │       6 cols        │
└─────────────────────┴─────────────────────┘

col-md-4 + col-md-4 + col-md-4:
┌─────────────┬─────────────┬─────────────┐
│   4 cols    │   4 cols    │   4 cols    │
└─────────────┴─────────────┴─────────────┘

Mobile (stacked):
┌─────────────────────────────────┐
│           12 cols               │
├─────────────────────────────────┤
│           12 cols               │
├─────────────────────────────────┤
│           12 cols               │
└─────────────────────────────────┘
```

## 🎯 Summary

### Key Principles
1. **Mobile First**: Design for small screens first
2. **Progressive Enhancement**: Add features for larger screens
3. **Touch Friendly**: 44px minimum touch targets
4. **Readable**: 16px minimum font size on mobile
5. **Flexible**: Use relative units (rem, em, %)
6. **Overflow**: Handle with horizontal scroll
7. **Performance**: Optimize for fast loading
8. **Accessible**: WCAG compliant

### Testing Checklist
- [ ] Test on real devices
- [ ] Check all breakpoints
- [ ] Verify touch targets
- [ ] Test forms
- [ ] Check tables
- [ ] Verify images
- [ ] Test navigation
- [ ] Check modals

---

**Remember**: Every component should work beautifully on every device size!
