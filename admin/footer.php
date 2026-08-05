            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script>
    document.getElementById('grinSidebarToggle').addEventListener('click', function() {
        document.getElementById('grinSidebar').classList.toggle('collapsed');
    });

    var notifBtn = document.getElementById('notificationBtn');
    var notifDrop = document.getElementById('notificationDropdown');
    if (notifBtn && notifDrop) {
        notifBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            notifDrop.classList.toggle('show');
            var profDrop = document.getElementById('profileDropdown');
            if (profDrop) profDrop.classList.remove('show');
        });
    }

    var profBtn = document.getElementById('profileDropdownBtn');
    var profDrop = document.getElementById('profileDropdown');
    if (profBtn && profDrop) {
        profBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            profDrop.classList.toggle('show');
            if (notifDrop) notifDrop.classList.remove('show');
        });
    }

    document.addEventListener('click', function() {
        if (notifDrop) notifDrop.classList.remove('show');
        if (profDrop) profDrop.classList.remove('show');
    });

    function markAllRead(e) {
        e.preventDefault();
        fetch('api/notifications-api.php?action=mark_all_read', { method: 'POST' })
            .then(function() { location.reload(); });
    }

    function showAddModal(modalId) {
        document.getElementById(modalId).classList.add('show');
    }

    function hideModal(modalId) {
        document.getElementById(modalId).classList.remove('show');
    }

    function showEditModal(modalId, id) {
        var form = document.getElementById(modalId).querySelector('form');
        if (form) form.action += '?id=' + id;
        document.getElementById(modalId).classList.add('show');
    }

    function deleteRecord(url, id) {
        if (confirm('Are you sure you want to delete this record?')) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'delete_id';
            input.value = id;
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
    }
    </script>
</body>
</html>
