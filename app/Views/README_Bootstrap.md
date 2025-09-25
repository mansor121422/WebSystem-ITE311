# Bootstrap Integration Guide

## Overview
This project now includes Bootstrap 5 integration with a modern, responsive template system.

## Files Created
- `template.php` - Main template file with Bootstrap CDN and navigation
- `home.php` - Sample page demonstrating Bootstrap components
- `README_Bootstrap.md` - This documentation file

## Features Included

### 1. Bootstrap 5 CDN
- Latest Bootstrap CSS and JavaScript (v5.3.2)
- Bootstrap Icons for enhanced UI elements
- Responsive design out of the box

### 2. Navigation Bar
- Dark theme with responsive hamburger menu
- Brand logo with icon
- Navigation links with icons
- Mobile-friendly collapse functionality

### 3. Template System
- Uses CodeIgniter 4's template inheritance
- `$this->extend('template')` to extend the base template
- `$this->section('content')` to define content areas
- `$this->endSection()` to close content sections

### 4. Bootstrap Components Demonstrated
- **Cards**: Feature cards with hover effects
- **Buttons**: Various button styles and sizes
- **Alerts**: Dismissible success alerts
- **Progress Bars**: Animated and static progress indicators
- **Badges**: Contextual badges for buttons
- **Grid System**: Responsive columns and rows
- **Typography**: Bootstrap utility classes for text

## How to Use

### Creating a New Page
1. Create a new view file (e.g., `about.php`)
2. Extend the template: `<?= $this->extend('template') ?>`
3. Define content section: `<?= $this->section('content') ?>`
4. Add your HTML content
5. Close the section: `<?= $this->endSection() ?>`

### Example Page Structure
```php
<?= $this->extend('template') ?>

<?= $this->section('content') ?>
    <!-- Your content here -->
    <div class="container">
        <h1>Page Title</h1>
        <p>Your content goes here...</p>
    </div>
<?= $this->endSection() ?>
```

### Passing Data to Views
```php
// In your controller
public function index(): string
{
    $data = [
        'title' => 'Page Title - App Name'
    ];
    return view('page_name', $data);
}
```

## Bootstrap Classes Used

### Layout
- `container` - Responsive container
- `row` - Bootstrap grid row
- `col-md-4` - Responsive columns
- `py-5` - Padding utilities
- `mb-4` - Margin utilities

### Components
- `navbar` - Navigation bar
- `card` - Card components
- `btn` - Button styles
- `alert` - Alert messages
- `progress` - Progress bars
- `badge` - Badge elements

### Utilities
- `text-center` - Text alignment
- `bg-dark` - Background colors
- `text-white` - Text colors
- `shadow-sm` - Shadow effects
- `rounded` - Border radius

## Testing
1. Navigate to your project URL (e.g., `http://localhost/ITE311-ALALAWI`)
2. You should see the Bootstrap-styled homepage
3. Test responsive design by resizing your browser
4. Check that the navigation menu collapses on mobile

## Customization
- Modify colors in the CSS section of `template.php`
- Add custom Bootstrap components as needed
- Extend the template with additional sections
- Customize the navigation menu items

## Browser Support
- Bootstrap 5 supports all modern browsers
- IE 11 and below are not supported
- Mobile-first responsive design

## Resources
- [Bootstrap 5 Documentation](https://getbootstrap.com/docs/5.3/)
- [Bootstrap Icons](https://icons.getbootstrap.com/)
- [CodeIgniter 4 Views](https://codeigniter4.github.io/userguide/outgoing/views.html)
