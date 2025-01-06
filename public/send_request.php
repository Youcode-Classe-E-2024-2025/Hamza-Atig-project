<?php
session_start();

class Database {
    private $conn;

    public function __construct() {
        include BASE_PATH . 'config/database.php';
        $this->conn = new mysqli($host, $username, $password, $dbname);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function query($query) {
        return $this->conn->query($query);
    }

    public function prepare($query) {
        return $this->conn->prepare($query);
    }

    public function close() {
        $this->conn->close();
    }
}

class Member {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function getChefs() {
        $query = "SELECT user_id, username, team_name FROM users WHERE role = 'Chef'";
        $result = $this->db->query($query);
        $chefs = [];
        while ($row = $result->fetch_assoc()) {
            $chefs[] = $row;
        }
        return $chefs;
    }

    public function getJoinRequests($memberId) {
        $query = "SELECT jr.request_id, jr.receiver_id, jr.status, u.username, u.team_name 
                  FROM join_requests jr
                  JOIN users u ON jr.receiver_id = u.user_id
                  WHERE jr.user_id = ?";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            die("Prepare failed: " . $this->db->error);
        }

        $stmt->bind_param("i", $memberId);
        $stmt->execute();
        $result = $stmt->get_result();
        $joinRequests = [];
        while ($row = $result->fetch_assoc()) {
            $joinRequests[] = $row;
        }
        return $joinRequests;
    }

    public function sendJoinRequest($memberId, $chefId) {
        $query = "SELECT request_id FROM join_requests WHERE user_id = ? AND receiver_id = ?";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            die("Prepare failed: " . $this->db->error);
        }
        $stmt->bind_param("ii", $memberId, $chefId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $query = "INSERT INTO join_requests (user_id, receiver_id, status) VALUES (?, ?, 'pending')";
            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                die("Prepare failed: " . $this->db->error);
            }
            $stmt->bind_param("ii", $memberId, $chefId);
            return $stmt->execute();
        } else {
            return false;
        }
    }
}

// Main Logic
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: login.php");
    exit();
}

$db = new Database();
$member = new Member($db);

$memberId = $_SESSION['user_id'];
$chefs = $member->getChefs();
$joinRequests = $member->getJoinRequests($memberId);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_request'])) {
    $chefId = $_POST['chef_id'];

    if ($member->sendJoinRequest($memberId, $chefId)) {
        $success_message = "Join request sent successfully!";
    } else {
        $error_message = "You have already sent a request to this chef.";
    }
}

$db->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Join Request</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>../assets/css/tailwind.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex">
    <!-- Sidebar -->
    <aside class="bg-gray-800 text-white w-64 min-h-screen p-6">
        <h2 class="text-xl font-semibold mb-6">Member Dashboard</h2>
        <ul class="space-y-4">
            <li>
                <a href="member.php" class="block text-gray-300 hover:text-white">Dashboard</a>
            </li>
            <li>
                <a href="send_request.php" class="block text-gray-300 hover:text-white">Join Teams</a>
            </li>
            <li>
                <a href="work.php" class="block text-gray-300 hover:text-white">start working</a>
            </li>
            <li>
                <a href="logout.php" class="block text-gray-300 hover:text-white">Logout</a>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Send Join Request</h1>

        <?php if (isset($success_message)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <!-- List of Teams and Chefs -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Available Teams</h2>
            <?php if (empty($chefs)): ?>
                <p class="text-gray-600">No teams available.</p>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($chefs as $chef): ?>
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                <?php echo htmlspecialchars($chef['team_name']); ?>
                            </h3>
                            <p class="text-gray-600 mb-4">
                                Chef: <?php echo htmlspecialchars($chef['username']); ?>
                            </p>
                            <form method="POST">
                                <input type="hidden" name="chef_id" value="<?php echo $chef['user_id']; ?>">
                                <button type="submit" name="send_request"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                                    Send Join Request
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- List of Join Requests -->
        <div>
            <h2 class="text-xl font-semibold text-gray-800 mb-4">My Join Requests</h2>
            <?php if (empty($joinRequests)): ?>
                <p class="text-gray-600">No join requests sent.</p>
            <?php else: ?>
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Team
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Chef
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($joinRequests as $request): ?>
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($request['team_name']); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            <?php echo htmlspecialchars($request['username']); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-sm font-semibold rounded-full 
                                            <?php
                                            switch ($request['status']) {
                                                case 'pending':
                                                    echo 'bg-yellow-100 text-yellow-800';
                                                    break;
                                                case 'approved':
                                                    echo 'bg-green-100 text-green-800';
                                                    break;
                                                case 'rejected':
                                                    echo 'bg-red-100 text-red-800';
                                                    break;
                                                default:
                                                    echo 'bg-gray-100 text-gray-800';
                                            }
                                            ?>">
                                            <?php echo ucfirst($request['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>