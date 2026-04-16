# HireMatrix Responsive Design - Quick Reference

## 🎯 Quick Start

### Testing Your Changes
1. Open: `http://localhost/ai-job-portal/public/responsive-test.html`
2. Resize browser window to test breakpoints
3. Use Chrome DevTools Device Mode (F12 → Toggle Device Toolbar)

### Key Files
- `public/jobboard/css/responsive.css` - Main responsive rules
- `public/jobboard/css/responsive-enhancements.css` - Additional enhancements
- All headers include both files automatically

## 📱 Breakpoints Cheat Sheet

```css
/* Extra Small (Mobile) */
@media (max-width: 479.98px) { }

/* Small (Phone) */
@media (min-width: 480px) and (max-width: 767.98px) { }

/* Medium (Tablet) */
@media (min-width: 768px) and (max-width: 991.98px) { }

/* Large (Laptop) */
@media (min-width: 992px) and (max-width: 1199.98px) { }

/* Extra Large (Desktop) */
@media (min-width: 1200px) { }
```

## 🔧 Common Patterns

### Make Element Full Width on Mobile
```css
@media (max-width: 767.98px) {
    .your-element {
        width: 100% !important;
    }
}
```

### Stack Columns on Mobile
```html
<div class="row">
    <div class="col-12 col-md-6">Column 1</div>
    <div class="col-12 col-md-6">Column 2</div>
</div>
```

### Hide Element on Mobile
```html
<div class="d-none d-md-block">Desktop only</div>
<div class="d-block d-md-none">Mobile only</div>
```

### Responsive Table
```html
<div class="table-responsive">
    <table class="table">
        <!-- table content -->
    </table>
</div>
```

### Responsive Font Size
```css
.title {
    font-size: clamp(1.5rem, 5vw, 2.5rem);
}
```

### Touch-Friendly Buttons
```css
.btn {
    min-height: 44px;
    min-width: 44px;
}
```

## 🎨 Utility Classes

### Display
- `d-none` - Hide on all
- `d-block` - Show as block
- `d-md-none` - Hide on medium and up
- `d-lg-block` - Show on large and up

### Flex
- `flex-md-column` - Column on medium and up
- `flex-wrap` - Allow wrapping
- `justify-content-center` - Center items

### Spacing
- `mb-3` - Margin bottom 1rem
- `mb-md-4` - Margin bottom 1.5rem on medium+
- `p-2` - Padding 0.5rem
- `py-4` - Padding top/bottom 1.5rem

### Text
- `text-center` - Center text
- `text-md-left` - Left align on medium+
- `text-truncate` - Truncate with ellipsis

## ⚠️ Common Mistakes to Avoid

### ❌ DON'T
```css
/* Fixed width without max-width */
.container {
    width: 1200px;
}

/* Tiny touch targets */
.btn {
    width: 20px;
    height: 20px;
}

/* No overflow handling */
table {
    width: 1000px;
}
```

### ✅ DO
```css
/* Flexible width */
.container {
    max-width: 1200px;
    width: 100%;
}

/* Adequate touch targets */
.btn {
    min-width: 44px;
    min-height: 44px;
}

/* Overflow handling */
.table-wrap {
    overflow-x: auto;
}
table {
    min-width: 600px;
}
```

## 🧪 Testing Checklist

### Before Committing
- [ ] Test on Chrome mobile view (375px, 768px, 1024px)
- [ ] Test on actual mobile device if possible
- [ ] Check horizontal scroll works below 360px
- [ ] Verify touch targets are at least 44px
- [ ] Ensure text is readable (min 16px on mobile)
- [ ] Test forms don't cause iOS zoom
- [ ] Check tables scroll horizontally
- [ ] Verify images scale properly

### Device Sizes to Test
- iPhone SE: 375px
- iPhone 12/13: 390px
- Samsung Galaxy: 360px
- iPad: 768px
- iPad Pro: 1024px
- Desktop: 1920px

## 🚀 Performance Tips

### Optimize Images
```html
<img src="image.jpg" 
     srcset="image-small.jpg 480w, 
             image-medium.jpg 768w, 
             image-large.jpg 1200w"
     sizes="(max-width: 768px) 100vw, 50vw"
     alt="Description">
```

### Lazy Load Images
```html
<img src="image.jpg" loading="lazy" alt="Description">
```

### Minimize CSS
- Use shorthand properties
- Combine similar media queries
- Remove unused styles

## 📚 Resources

### Documentation
- [Bootstrap 4 Grid](https://getbootstrap.com/docs/4.6/layout/grid/)
- [CSS Media Queries](https://developer.mozilla.org/en-US/docs/Web/CSS/Media_Queries)
- [Responsive Images](https://developer.mozilla.org/en-US/docs/Learn/HTML/Multimedia_and_embedding/Responsive_images)

### Tools
- Chrome DevTools Device Mode
- Firefox Responsive Design Mode
- [Responsively App](https://responsively.app/)
- [BrowserStack](https://www.browserstack.com/)

## 🆘 Troubleshooting

### Issue: Content Overflows Container
**Check:**
1. Is there a fixed width without max-width?
2. Are there long words without word-break?
3. Is there a table without overflow handling?

**Fix:**
```css
.element {
    max-width: 100%;
    word-break: break-word;
    overflow-wrap: break-word;
}
```

### Issue: Horizontal Scroll Appears
**Check:**
1. Elements with width > 100vw
2. Negative margins
3. Absolute positioned elements

**Fix:**
```css
body {
    overflow-x: hidden; /* Only if necessary */
}
.element {
    max-width: 100%;
}
```

### Issue: Text Too Small on Mobile
**Fix:**
```css
body {
    font-size: 16px; /* Prevents iOS zoom */
}
.title {
    font-size: clamp(1.5rem, 5vw, 2.5rem);
}
```

### Issue: Buttons Too Small to Tap
**Fix:**
```css
.btn {
    min-height: 44px;
    min-width: 44px;
    padding: 10px 20px;
}
```

## 💡 Pro Tips

1. **Mobile First**: Start with mobile styles, then add desktop
2. **Test Early**: Don't wait until the end to test responsive
3. **Use Flexbox/Grid**: Modern layout tools are responsive by default
4. **Avoid Fixed Heights**: Let content determine height
5. **Use Relative Units**: rem, em, %, vw, vh instead of px
6. **Touch Targets**: Minimum 44px × 44px for all interactive elements
7. **Font Size**: Minimum 16px on mobile to prevent iOS zoom
8. **Images**: Always use max-width: 100% and height: auto

## 🔄 Update Process

### When Adding New Features
1. Write mobile styles first
2. Add tablet breakpoint adjustments
3. Add desktop enhancements
4. Test on all breakpoints
5. Test on real devices

### When Fixing Bugs
1. Identify affected breakpoint
2. Test fix on all breakpoints
3. Verify no regression
4. Update documentation if needed

## 📞 Support

**Questions?** Check:
1. This quick reference
2. `RESPONSIVE_DESIGN.md` (full documentation)
3. Existing CSS files for patterns
4. Ask the development team

---

**Remember**: Every new feature should be responsive from day one!
