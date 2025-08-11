<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Approve Pelaporan</title>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            border-bottom: 2px solid #007bff;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
        }
        .post-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 20px;
            padding: 20px;
            background: #f9f9f9;
        }
        .post-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        .post-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin: 0 0 5px 0;
        }
        .post-meta {
            color: #666;
            font-size: 14px;
        }
        .post-status {
            background: #ffc107;
            color: #333;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .post-description {
            margin: 15px 0;
            color: #555;
            line-height: 1.5;
        }
        .post-location {
            color: #007bff;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .post-image {
            max-width: 300px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
        }
        .btn-approve {
            background: #28a745;
            color: white;
        }
        .btn-approve:hover {
            background: #218838;
        }
        .btn-reject {
            background: #dc3545;
            color: white;
        }
        .btn-reject:hover {
            background: #c82333;
        }
        .alert {
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .no-posts {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 40px;
        }
        .reject-form {
            display: none;
            margin-top: 10px;
            padding: 15px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .reject-form textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .reject-form button {
            margin-top: 10px;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚗 Admin - Approve Pelaporan Parkir</h1>
            <p>Kelola pelaporan yang masuk dan butuh persetujuan</p>
        </div>



        @if($pendingPosts->count() > 0)
            @foreach($pendingPosts as $post)
                <div class="post-card">
                    <div class="post-header">
                        <div>
                            <h3 class="post-title">{{ $post->title }}</h3>
                            <div class="post-meta">
                                Oleh: {{ $post->hide_identity ? 'Anonymous' : $post->user->username }} | 
                                Kategori: {{ $post->category->category_name }} | 
                                {{ $post->created_at->format('d M Y H:i') }}
                            </div>
                        </div>
                        <span class="post-status">{{ strtoupper($post->status) }}</span>
                    </div>

                    <div class="post-location">
                        📍 {{ $post->location_name }}
                    </div>

                    <div class="post-description">
                        {{ $post->description }}
                    </div>

                    @if($post->photo_url)
                        <img src="{{ $post->photo_url }}" alt="Foto pelaporan" class="post-image">
                    @endif

                    <div class="action-buttons">
                        <button type="button" class="btn btn-approve" onclick="confirmApprove({{ $post->post_id }})">
                            ✅ Approve
                        </button>

                        <button type="button" class="btn btn-reject" onclick="showRejectDialog({{ $post->post_id }})">
                            ❌ Reject
                        </button>
                    </div>

                    <!-- Hidden forms untuk submit -->
                    <form id="approve-form-{{ $post->post_id }}" action="{{ route('admin.posts.approve', $post->post_id) }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    
                    <form id="reject-form-{{ $post->post_id }}" action="{{ route('admin.posts.reject', $post->post_id) }}" method="POST" style="display: none;">
                        @csrf
                        <input type="hidden" name="rejection_reason" id="rejection-reason-{{ $post->post_id }}">
                    </form>
                </div>
            @endforeach
        @else
            <div class="no-posts">
                🎉 Tidak ada pelaporan yang perlu di-approve!
                <br>Semua post sudah diproses.
            </div>
        @endif
    </div>

    <script>
        // Show success/error messages dengan SweetAlert
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Oops!',
                text: '{{ session('error') }}',
                confirmButtonText: 'OK'
            });
        @endif

        // Confirm approve dengan SweetAlert
        function confirmApprove(postId) {
            Swal.fire({
                title: 'Approve Pelaporan?',
                text: 'Yakin mau approve pelaporan ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '✅ Ya, Approve!',
                cancelButtonText: '❌ Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Sedang memproses...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Submit form
                    document.getElementById('approve-form-' + postId).submit();
                }
            });
        }

        // Show reject dialog dengan input
        function showRejectDialog(postId) {
            Swal.fire({
                title: 'Reject Pelaporan',
                html: `
                    <div style="text-align: left;">
                        <label for="swal-input" style="display: block; margin-bottom: 8px; font-weight: bold;">Alasan Penolakan:</label>
                        <textarea 
                            id="swal-input" 
                            class="swal2-input" 
                            placeholder="Jelaskan kenapa pelaporan ini ditolak..." 
                            rows="4"
                            style="height: auto; resize: vertical; width: 100%; box-sizing: border-box;"
                        ></textarea>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '❌ Tolak Post',
                cancelButtonText: '🔙 Batal',
                reverseButtons: true,
                focusConfirm: false,
                preConfirm: () => {
                    const reason = document.getElementById('swal-input').value;
                    if (!reason || reason.trim() === '') {
                        Swal.showValidationMessage('Alasan penolakan harus diisi!');
                        return false;
                    }
                    if (reason.length > 500) {
                        Swal.showValidationMessage('Alasan penolakan maksimal 500 karakter!');
                        return false;
                    }
                    return reason;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Set rejection reason
                    document.getElementById('rejection-reason-' + postId).value = result.value;
                    
                    // Show loading
                    Swal.fire({
                        title: 'Sedang memproses...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Submit form
                    document.getElementById('reject-form-' + postId).submit();
                }
            });
        }
    </script>
</body>
</html>
