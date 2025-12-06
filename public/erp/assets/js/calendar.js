eventDrop: function(info) {
    const checklistId = info.event.extendedProps.checklistId;
    
    // Use local date formatting instead of UTC to prevent timezone shift
    const formatLocalDate = (date) => {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };
    
    const startDate = formatLocalDate(info.event.start);
    const endDate = info.event.end ? formatLocalDate(info.event.end) : startDate;
    const startTime = info.event.start.toTimeString().split(' ')[0];
    const endTime = info.event.end ? info.event.end.toTimeString().split(' ')[0] : '23:59:59';

    const formData = new FormData();
    formData.append('checklist_id', checklistId);
    formData.append('start_date', startDate);
    formData.append('end_date', endDate);
    formData.append('start_time', startTime);
    formData.append('end_time', endTime);

    fetch('update_checklist_dates.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            info.revert();
            alert('Failed to update dates: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        info.revert();
        alert('Failed to update dates');
    });
},
eventResize: function(info) {
    const checklistId = info.event.extendedProps.checklistId;
    
    // Use local date formatting instead of UTC to prevent timezone shift
    const formatLocalDate = (date) => {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };
    
    const startDate = formatLocalDate(info.event.start);
    const endDate = info.event.end ? formatLocalDate(info.event.end) : startDate;
    const startTime = info.event.start.toTimeString().split(' ')[0];
    const endTime = info.event.end ? info.event.end.toTimeString().split(' ')[0] : '23:59:59';

    const formData = new FormData();
    formData.append('checklist_id', checklistId);
    formData.append('start_date', startDate);
    formData.append('end_date', endDate);
    formData.append('start_time', startTime);
    formData.append('end_time', endTime);

    fetch('update_checklist_dates.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            info.revert();
            alert('Failed to update dates: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        info.revert();
        alert('Failed to update dates');
    });
} 