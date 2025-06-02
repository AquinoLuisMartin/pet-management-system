<?php
include "includes/db_conn.php";
include "includes/header.php";

// Fetch all appointments for the calendar
$stmt = $conn->prepare("CALL GetAllAppointmentsForCalendar()");
$stmt->execute();
$appointments_result = $stmt->get_result();
$appointments = [];

// Format appointments for the calendar
while ($row = $appointments_result->fetch_assoc()) {
    $appointments[] = [
        'id' => $row['AppointmentID'],
        'title' => $row['PetName'] . ' - ' . $row['Reason'],
        'start' => $row['Date'] . 'T' . $row['Time'],
        'status' => $row['Status'],
        'pet' => $row['PetName'],
        'owner' => $row['OwnerName'],
        'vet' => $row['VetName'],
        'reason' => $row['Reason'],
        'email' => $row['Email'] ?? '',
        'phone' => $row['Phone'] ?? '',
        'species' => $row['Species'] ?? '',
        'breed' => $row['Breed'] ?? ''
    ];
}

// Convert to JSON for JavaScript
$appointments_json = json_encode($appointments);
?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h1><i class="fas fa-calendar-alt"></i> Appointment Calendar</h1>
            <p class="text-muted">View all scheduled appointments in calendar format</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Calendar will appear here -->
            <div class="card mb-4">
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <!-- Appointment details will appear here -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Appointment Details</h5>
                </div>
                <div class="card-body" id="appointment-details">
                    <div class="text-center p-4">
                        <i class="fas fa-calendar-day fa-3x text-muted mb-3"></i>
                        <p>Select a day or appointment from the calendar to view details</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add FullCalendar library -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Parse appointments data
    const appointments = <?php echo $appointments_json; ?>;
    
    // Initialize calendar
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: appointments.map(appointment => {
            // Set color based on status
            let color;
            switch(appointment.status) {
                case 'Completed':
                    color = '#28a745'; // green
                    break;
                case 'Scheduled':
                    color = '#007bff'; // blue
                    break;
                case 'Cancelled':
                    color = '#dc3545'; // red
                    break;
                default:
                    color = '#6c757d'; // gray
            }
            
            return {
                id: appointment.id,
                title: appointment.title,
                start: appointment.start,
                backgroundColor: color,
                borderColor: color,
                extendedProps: {
                    pet: appointment.pet,
                    owner: appointment.owner,
                    vet: appointment.vet,
                    reason: appointment.reason,
                    status: appointment.status,
                    email: appointment.email,
                    phone: appointment.phone,
                    species: appointment.species,
                    breed: appointment.breed
                }
            };
        }),
        eventClick: function(info) {
            // Show appointment details
            displayAppointmentDetails(info.event);
        },
        dateClick: function(info) {
            // Show all appointments for this day
            const clickedDate = info.dateStr;
            const appointmentsForDay = appointments.filter(appointment => 
                appointment.start.startsWith(clickedDate));
            
            displayDayAppointments(clickedDate, appointmentsForDay);
        }
    });
    
    calendar.render();
    
    // Function to display appointment details
    function displayAppointmentDetails(event) {
        const props = event.extendedProps;
        const statusClass = getStatusClass(props.status);
        
        const detailsHtml = `
            <h5>${props.pet}</h5>
            <div class="mb-3">
                <span class="badge ${statusClass}">${props.status}</span>
            </div>
            <table class="table table-sm">
                <tr>
                    <th>Owner:</th>
                    <td>${props.owner}</td>
                </tr>
                <tr>
                    <th>Contact:</th>
                    <td>
                        <i class="fas fa-envelope"></i> ${props.email}<br>
                        <i class="fas fa-phone"></i> ${props.phone}
                    </td>
                </tr>
                <tr>
                    <th>Pet:</th>
                    <td>${props.pet} (${props.species} - ${props.breed})</td>
                </tr>
                <tr>
                    <th>Veterinarian:</th>
                    <td>${props.vet}</td>
                </tr>
                <tr>
                    <th>Reason:</th>
                    <td>${props.reason}</td>
                </tr>
                <tr>
                    <th>Date & Time:</th>
                    <td>${formatDateTime(event.start)}</td>
                </tr>
            </table>
        `;
        
        document.getElementById('appointment-details').innerHTML = detailsHtml;
    }
    
    // Function to display all appointments for a specific day
    function displayDayAppointments(date, dayAppointments) {
        if (dayAppointments.length === 0) {
            document.getElementById('appointment-details').innerHTML = `
                <div class="text-center p-4">
                    <i class="fas fa-calendar-day fa-3x text-muted mb-3"></i>
                    <h5>No appointments</h5>
                    <p>There are no appointments scheduled for ${formatDate(new Date(date))}.</p>
                </div>
            `;
            return;
        }
        
        let html = `<h5>${formatDate(new Date(date))}</h5>
                    <p>${dayAppointments.length} appointment(s) scheduled</p>
                    <div class="list-group">`;
        
        dayAppointments.forEach(appointment => {
            const statusClass = getStatusClass(appointment.status);
            html += `
                <a href="#" class="list-group-item list-group-item-action" onclick="showAppointmentDetails('${appointment.id}'); return false;">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1">${appointment.pet}</h6>
                        <small>${formatTime(appointment.start)}</small>
                    </div>
                    <p class="mb-1">${appointment.reason}</p>
                    <div class="d-flex justify-content-between">
                        <small>Dr. ${appointment.vet}</small>
                        <span class="badge ${statusClass}">${appointment.status}</span>
                    </div>
                </a>
            `;
        });
        
        html += '</div>';
        document.getElementById('appointment-details').innerHTML = html;
    }
    
    // Helper function to get Bootstrap status class
    function getStatusClass(status) {
        switch (status) {
            case 'Completed':
                return 'bg-success';
            case 'Scheduled':
                return 'bg-primary';
            case 'Cancelled':
                return 'bg-danger';
            default:
                return 'bg-secondary';
        }
    }
    
    // Helper function to format date nicely
    function formatDate(date) {
        return date.toLocaleDateString('en-US', { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
    }
    
    // Helper function to format time
    function formatTime(datetime) {
        const time = datetime.split('T')[1];
        const date = new Date(`2000-01-01T${time}`);
        return date.toLocaleTimeString('en-US', { 
            hour: 'numeric', 
            minute: 'numeric',
            hour12: true 
        });
    }
    
    // Helper function to format date and time
    function formatDateTime(date) {
        return date.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric'
        }) + ' at ' + date.toLocaleTimeString('en-US', { 
            hour: 'numeric', 
            minute: 'numeric',
            hour12: true 
        });
    }
    
    // Expose function to global scope for list item clicks
    window.showAppointmentDetails = function(appointmentId) {
        const appointment = appointments.find(a => a.id === appointmentId);
        if (appointment) {
            const event = calendar.getEventById(appointmentId);
            if (event) {
                displayAppointmentDetails(event);
            }
        }
    };
});
</script>



<?php include "includes/footer.php"; ?>