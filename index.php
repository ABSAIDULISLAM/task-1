<?php
require_once 'classes/User.php';
$userObj = new User();

// Handle form submission for creating a user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $uploadDir = 'uploads/';
    $fileName = basename($_FILES['picture']['name']);
    $filePath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['picture']['tmp_name'], $filePath)) {
        $data = [
            'name' => $_POST['name'],
            'phone' => $_POST['phone'],
            'email' => $_POST['email'],
            'address' => $_POST['address'],
            'picture' => $fileName
        ];
        $userObj->createUser($data);
    }
    header('Location: index.php');
}

// Handle delete request
if (isset($_GET['delete_id'])) {
    $userObj->deleteUser($_GET['delete_id']);
    header('Location: index.php');
}

$users = $userObj->getAllUsers();


// update code 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $uploadDir = 'uploads/';
    $fileName = !empty($_FILES['picture']['name']) ? basename($_FILES['picture']['name']) : null;

    // নতুন ছবি আপলোড হলে
    if ($fileName) {
        // পুরানো ছবি ডিলিট করা
        if (!empty($_POST['existing_picture']) && file_exists($uploadDir . $_POST['existing_picture'])) {
            unlink($uploadDir . $_POST['existing_picture']);
        }

        // নতুন ছবি সেভ করা
        $filePath = $uploadDir . $fileName;
        move_uploaded_file($_FILES['picture']['tmp_name'], $filePath);
    }

    $data = [
        'id' => $_POST['id'], // সঠিক ID পাঠানো হচ্ছে কিনা চেক করুন
        'name' => $_POST['name'],
        'phone' => $_POST['phone'],
        'email' => $_POST['email'],
        'address' => $_POST['address'],
        'picture' => $fileName ?: $_POST['existing_picture']
    ];

    // ইউজার আপডেট
    if ($userObj->updateUser($data)) {
        header('Location: index.php');
    } else {
        die('Error: Update failed.');
    }
}



?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CRUD App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-3">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h2>CRUD Table</h2>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">Create New</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Address</th>
                                    <th>Picture</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?= $user['id'] ?></td>
                                        <td><?= htmlspecialchars($user['name']) ?></td>
                                        <td><?= htmlspecialchars($user['phone']) ?></td>
                                        <td><?= htmlspecialchars($user['email']) ?></td>
                                        <td><?= htmlspecialchars($user['address']) ?></td>
                                        <td><img src="uploads/<?= htmlspecialchars($user['picture']) ?>" width="50" alt="Picture"></td>
                                        <td>
                                            <a onclick="return confirm('Are you sure to delete this item..?')" href="?delete_id=<?= $user['id'] ?>" class="btn btn-danger btn-sm">Delete</a>
                                            <button type="button" class="btn btn-warning btn-sm edit-button" 
                                                data-id="<?= $user['id'] ?>"
                                                data-name="<?= htmlspecialchars($user['name']) ?>"
                                                data-phone="<?= htmlspecialchars($user['phone']) ?>"
                                                data-email="<?= htmlspecialchars($user['email']) ?>"
                                                data-address="<?= htmlspecialchars($user['address']) ?>"
                                                data-picture="<?= htmlspecialchars($user['picture']) ?>"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editModal">
                                                Edit
                                            </button>
                                        </td>   

                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Create User</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="picture" class="form-label">Picture</label>
                        <input type="file" class="form-control" id="picture" name="picture" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- edit modal  -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editModalLabel">Edit User</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="POST" enctype="multipart/form-data">
                    <input type="hidden" id="editId" name="id">
                    <div class="mb-3">
                        <label for="editName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="editName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editPhone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="editPhone" name="phone" required>
                    </div>
                    <div class="mb-3">
                        <label for="editEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="editEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="editAddress" class="form-label">Address</label>
                        <textarea class="form-control" id="editAddress" name="address" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editPicture" class="form-label">Picture</label>
                        <input type="file" class="form-control" id="editPicture" name="picture">
                        <img id="editPicturePreview" width="50" class="mt-2" alt="Picture">
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    document.querySelectorAll('.edit-button').forEach(button => {
        button.addEventListener('click', function () {
            const userId = this.dataset.id;
            const userName = this.dataset.name;
            const userPhone = this.dataset.phone;
            const userEmail = this.dataset.email;
            const userAddress = this.dataset.address;
            const userPicture = this.dataset.picture;

            // Populate modal fields
            document.getElementById('editId').value = userId;
            document.getElementById('editName').value = userName;
            document.getElementById('editPhone').value = userPhone;
            document.getElementById('editEmail').value = userEmail;
            document.getElementById('editAddress').value = userAddress;
            document.getElementById('editPicturePreview').src = 'uploads/' + userPicture;
        });
    });
</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
