import './bootstrap';
// Import Bootstrap (likely already here, but good to ensure)

// Import FullCalendar core and the DayGrid plugin
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';

// Wait for the DOM to be fully loaded before running any scripts
document.addEventListener('DOMContentLoaded', function() {
    
    // --- Tenant Calendar Initialization ---
    // Find the calendar element on the page
    const calendarEl = document.getElementById('tenant-calendar');
    
    // IMPORTANT: Only run this code if the calendar element exists on the current page
    // This prevents errors on other pages that don't have a calendar
    if (calendarEl) {
        
        // 1. Get the event data from the 'data-events' attribute
        const rawEvents = calendarEl.getAttribute('data-events');
        let events = [];
        try {
            // Parse the JSON string (from the controller) into a JavaScript array
            events = JSON.parse(rawEvents);
        } catch(e) {
            console.error("Failed to parse calendar events. Is the data valid JSON?", e);
            console.log("Raw Data:", rawEvents);
        }

        // 2. Set the initial date for the calendar
        // (Uses the first event's date or defaults to today)
        const initialDate = events.length > 0 ? events[0].start : new Date();

        // 3. Create the FullCalendar instance
        const calendar = new Calendar(calendarEl, {
            plugins: [ dayGridPlugin ], // Add the 'dayGridMonth' plugin
            height: 'auto',
            initialView: 'dayGridMonth',
            initialDate: initialDate,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: '' // Removed the 'dayGridMonth,timeGridWeek'
            },
            events: events, // Pass in the dynamic event data
            eventDidMount: function(info) {
                // This function styles events based on their title (Paid, Partial, Due)
                if (info.event.title.includes('Paid')) {
                    info.el.style.backgroundColor = '#1B5F99'; // tenant-blue-600
                    info.el.style.borderColor = '#1B5F99';
                } else if (info.event.title.includes('Partial')) {
                    info.el.style.backgroundColor = '#ffc107'; // Yellow
                    info.el.style.borderColor = '#ffc107';
                } else if (info.event.title.includes('Due')) {
                    info.el.style.backgroundColor = '#dc3545'; // Red
                    info.el.style.borderColor = '#dc3545';
                } else if (info.event.title.includes('Partial')) {
                    info.el.style.backgroundColor = '#ffc107'; // Yellow
                    info.el.style.borderColor = '#ffc107';
                } else if (info.event.title.includes('Due')) {
                    info.el.style.backgroundColor = '#dc3545'; // Red
                    info.el.style.borderColor = '#dc3545';
                }
                // === NEW: Style for Maintenance Events ===
                else if (info.event.title.includes('Service:')) {
                    info.el.style.backgroundColor = '#6f42c1'; // Bootstrap Purple
                    info.el.style.borderColor = '#6f42c1';
                    info.el.style.color = '#ffffff'; // White text
                }
            }
        });
        
        // --- SYNTAX ERROR WAS HERE ---
        // The extra '};' right before "Render the calendar" has been removed.
        // ---
        
        // 4. Render the calendar (NOW INSIDE THE IF BLOCK)
        calendar.render();

        // --- Sidebar Resize Logic ---
        // (Also moved inside the if-block to use the 'calendar' variable)
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggleSidebar');

        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener('click', () => {
                // Check if calendar exists before trying to resize it
                if (calendarEl) {
                    calendarEl.style.transition = 'opacity 0.3s ease';
                    calendarEl.style.opacity = '0.4';

                    // Wait for the sidebar animation to finish, then resize the calendar
                    setTimeout(() => {
                        calendar.updateSize(); // This is the FullCalendar API method to resize
                        calendarEl.style.opacity = '1';
                    }, 350); // 350ms matches the CSS transition time
                }
            });
        }

        // Re-render on window resize
        window.addEventListener('resize', () => {
            // Check if calendar exists before trying to resize it
            if (calendarEl) {
                calendar.updateSize();
            }
        });
    }

    // --- Global Sidebar Toggle Logic ---
    // This should run on every page, regardless of whether the calendar exists.
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggleSidebar');

    if (toggleBtn && sidebar) {
        // This handles the sidebar animation
        toggleBtn.addEventListener('click', () => {
            if (window.innerWidth > 768) {
              sidebar.classList.toggle('collapsed');
            } else {
              sidebar.classList.toggle('show');
            }
        });

        // Close sidebar when clicking outside (mobile)
        document.addEventListener("click", function(e) {
            if (window.innerWidth <= 768 && sidebar.classList.contains("show")) {
                if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
                    sidebar.classList.remove("show");
                }
            }
        });
    }
});