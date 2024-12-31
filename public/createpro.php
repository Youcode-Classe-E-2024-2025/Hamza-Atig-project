<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kanban Board</title>
    <link rel="stylesheet" href="../assets/css/tailwind.css">
</head>

<body class="bg-gray-100 min-h-screen p-6">
    <button data-drawer-target="default-sidebar" data-drawer-toggle="default-sidebar" aria-controls="default-sidebar"
        type="button"
        class="inline-flex items-center p-2 mt-2 ms-3 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
        <span class="sr-only">Open sidebar</span>
        <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
            xmlns="http://www.w3.org/2000/svg">
            <path clip-rule="evenodd" fill-rule="evenodd"
                d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z">
            </path>
        </svg>
    </button>

    <aside id="default-sidebar"
        class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full sm:translate-x-0"
        aria-label="Sidebar">
        <div class="h-full px-3 py-4 overflow-y-auto bg-gray-50 dark:bg-gray-800">
            <ul class="space-y-2 font-medium">
                <li>
                    <a href="./index.php"
                        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                            viewBox="0 0 22 21">
                            <path
                                d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z" />
                            <path
                                d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z" />
                        </svg>
                        <span class="ms-3">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="./addtask.php"
                        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                            viewBox="0 0 18 18">
                            <path
                                d="M6.143 0H1.857A1.857 1.857 0 0 0 0 1.857v4.286C0 7.169.831 8 1.857 8h4.286A1.857 1.857 0 0 0 8 6.143V1.857A1.857 1.857 0 0 0 6.143 0Zm10 0h-4.286A1.857 1.857 0 0 0 10 1.857v4.286C10 7.169 10.831 8 11.857 8h4.286A1.857 1.857 0 0 0 18 6.143V1.857A1.857 1.857 0 0 0 16.143 0Zm-10 10H1.857A1.857 1.857 0 0 0 0 11.857v4.286C0 17.169.831 18 1.857 18h4.286A1.857 1.857 0 0 0 8 16.143v-4.286A1.857 1.857 0 0 0 6.143 10Zm10 0h-4.286A1.857 1.857 0 0 0 10 11.857v4.286c0 1.026.831 1.857 1.857 1.857h4.286A1.857 1.857 0 0 0 18 16.143v-4.286A1.857 1.857 0 0 0 16.143 10Z" />
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Kanban</span>
                        <span
                            class="inline-flex items-center justify-center px-2 ms-3 text-sm font-medium text-gray-800 bg-gray-100 rounded-full dark:bg-gray-700 dark:text-gray-300">Pro</span>
                    </a>
                </li>
                <li>
                    <a href="#"
                        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                            viewBox="0 0 20 20">
                            <path
                                d="m17.418 3.623-.018-.008a6.713 6.713 0 0 0-2.4-.569V2h1a1 1 0 1 0 0-2h-2a1 1 0 0 0-1 1v2H9.89A6.977 6.977 0 0 1 12 8v5h-2V8A5 5 0 1 0 0 8v6a1 1 0 0 0 1 1h8v4a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-4h6a1 1 0 0 0 1-1V8a5 5 0 0 0-2.582-4.377ZM6 12H4a1 1 0 0 1 0-2h2a1 1 0 0 1 0 2Z" />
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Inbox</span>
                        <span
                            class="inline-flex items-center justify-center w-3 h-3 p-3 ms-3 text-sm font-medium text-blue-800 bg-blue-100 rounded-full dark:bg-blue-900 dark:text-blue-300">3</span>
                    </a>
                </li>
                <li>
                    <a href="#"
                        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                            viewBox="0 0 20 18">
                            <path
                                d="M14 2a3.963 3.963 0 0 0-1.4.267 6.439 6.439 0 0 1-1.331 6.638A4 4 0 1 0 14 2Zm1 9h-1.264A6.957 6.957 0 0 1 15 15v2a2.97 2.97 0 0 1-.184 1H19a1 1 0 0 0 1-1v-1a5.006 5.006 0 0 0-5-5ZM6.5 9a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9ZM8 10H5a5.006 5.006 0 0 0-5 5v2a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-2a5.006 5.006 0 0 0-5-5Z" />
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Users</span>
                    </a>
                </li>
                <li>
                    <a href="#"
                        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                            viewBox="0 0 18 20">
                            <path
                                d="M17 5.923A1 1 0 0 0 16 5h-3V4a4 4 0 1 0-8 0v1H2a1 1 0 0 0-1 .923L.086 17.846A2 2 0 0 0 2.08 20h13.84a2 2 0 0 0 1.994-2.153L17 5.923ZM7 9a1 1 0 0 1-2 0V7h2v2Zm0-5a2 2 0 1 1 4 0v1H7V4Zm6 5a1 1 0 1 1-2 0V7h2v2Z" />
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Products</span>
                    </a>
                </li>
                <li>
                    <a href="#"
                        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 16">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M1 8h11m0 0L8 4m4 4-4 4m4-11h3a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-3" />
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">logout</span>
                    </a>
                </li>
            </ul>
        </div>
    </aside>

    <button class="w-40 left-[20%] absolute bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full"
        onclick="openCreateProjectModal()">
        Create Project
    </button>
    <script>
        function openCreateProjectModal() {
            document.querySelector(".create-project-modal").style.display = "block";
        }
    </script>
    <div class="create-project-modal hidden fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
                <div class="bg-white px-6 pt-5 pb-4 sm:p-8">
                    <div class="sm:flex sm:items-start">
                        <div class="w-full">
                            <h3 class="text-2xl font-semibold text-gray-900 mb-6" id="modal-title">Create Project</h3>
                            <div class="mb-6">
                                <label for="project-name" class="block text-sm font-medium text-gray-700 mb-2">Project
                                    Name</label>
                                <input type="text" id="project-name"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                    placeholder="Enter project name" />
                            </div>
                            <div class="mb-6">
                                <label for="project-description"
                                    class="block text-sm font-medium text-gray-700 mb-2">Project Description</label>
                                <textarea id="project-description"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                    rows="4" placeholder="Enter project description"></textarea>
                            </div>
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Select Team Members</label>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full bg-white border border-gray-300 rounded-lg">
                                        <thead>
                                            <tr class="bg-gray-100">
                                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Name
                                                </th>
                                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Role
                                                </th>
                                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Action
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="team-members-table">
                                            <tr data-id="1" class="border-b border-gray-200 hover:bg-gray-50">
                                                <td class="px-4 py-2 text-sm text-gray-700">John Doe</td>
                                                <td class="px-4 py-2 text-sm text-gray-700">Developer</td>
                                                <td class="px-4 py-2 text-sm">
                                                    <button
                                                        class="px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200"
                                                        onclick="addMember('1', 'John Doe')">Add</button>
                                                </td>
                                            </tr>
                                            <tr data-id="2" class="border-b border-gray-200 hover:bg-gray-50">
                                                <td class="px-4 py-2 text-sm text-gray-700">Jane Smith</td>
                                                <td class="px-4 py-2 text-sm text-gray-700">Tester</td>
                                                <td class="px-4 py-2 text-sm">
                                                    <button
                                                        class="px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200"
                                                        onclick="addMember('2', 'Jane Smith')">Add</button>
                                                </td>
                                            </tr>
                                            <tr data-id="3" class="border-b border-gray-200 hover:bg-gray-50">
                                                <td class="px-4 py-2 text-sm text-gray-700">Michael Brown</td>
                                                <td class="px-4 py-2 text-sm text-gray-700">Designer</td>
                                                <td class="px-4 py-2 text-sm">
                                                    <button
                                                        class="px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200"
                                                        onclick="addMember('3', 'Michael Brown')">Add</button>
                                                </td>
                                            </tr>
                                            <tr data-id="4" class="border-b border-gray-200 hover:bg-gray-50">
                                                <td class="px-4 py-2 text-sm text-gray-700">Emily Davis</td>
                                                <td class="px-4 py-2 text-sm text-gray-700">WebSite Manager</td>
                                                <td class="px-4 py-2 text-sm">
                                                    <button
                                                        class="px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200"
                                                        onclick="addMember('4', 'Emily Davis')">Add</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="mb-8">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Selected Team
                                    Members</label>
                                <div id="selected-members" class="flex flex-wrap gap-2">
                                </div>
                            </div>
                            <div class="flex justify-end space-x-4">
                                <button type="button"
                                    class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 transition duration-200"
                                    onclick="document.querySelector('.create-project-modal').style.display = 'none'">Cancel</button>
                                <button type="button"
                                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">Create</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const colors = ['bg-blue-100', 'bg-green-100', 'bg-yellow-100', 'bg-pink-100', 'bg-purple-100'];
        function addMember(id, name) {
            const selectedMembersContainer = document.getElementById('selected-members');

            if (document.querySelector(`#selected-members [data-id="${id}"]`)) return;

            const chip = document.createElement('div');
            const color = colors[selectedMembersContainer.children.length % colors.length];
            chip.className = `flex items-center ${color} text-gray-700 px-3 py-1 rounded-full text-sm`;
            chip.setAttribute('data-id', id);
            chip.innerHTML = `
            ${name}
            <button onclick="removeMember('${id}', '${name}')" class="ml-2 text-gray-700 hover:text-gray-900 focus:outline-none">
                &times;
            </button>
        `;
            selectedMembersContainer.appendChild(chip);
            const row = document.querySelector(`#team-members-table tr[data-id="${id}"]`);
            row.style.display = 'none';
        }

        function removeMember(id, name) {
            const chip = document.querySelector(`#selected-members [data-id="${id}"]`);
            chip.remove();

            const row = document.querySelector(`#team-members-table tr[data-id="${id}"]`);
            row.style.display = 'table-row';
        }
    </script>

    <script>
        document.getElementById('add-member').addEventListener('click', function () {
            const select = document.getElementById('project-team');
            const selectedMembersContainer = document.getElementById('selected-members');
            const selectedOption = select.options[select.selectedIndex];
            if (!selectedOption.value) return;
            const chip = document.createElement('div');
            chip.className = 'flex items-center bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm';
            chip.innerHTML = `
            ${selectedOption.text}
            <button onclick="this.parentElement.remove()" class="ml-2 text-blue-700 hover:text-blue-900 focus:outline-none">
                &times;
            </button>
        `;
            selectedMembersContainer.appendChild(chip);
            select.selectedIndex = 0;
        });
    </script>
    <ul class="bg-white rounded-lg shadow divide-y divide-gray-200 w-[80%] top-[13%] left-[18%] absolute">
        <li class="px-6 py-4">
            <div class="flex justify-between">
                <a href="./addtask.php" class="font-semibold text-lg">Virtual Reality Project</a>
                <span class="text-gray-500 text-xs">Started 1 week ago</span>
            </div>
            <p class="text-gray-700">This project is about creating a 3D website using A-Frame. It is currently in
                progress.</p>
        </li>
        <li class="px-6 py-4">
            <div class="flex justify-between">
                <a href="./addtask.php" class="font-semibold text-lg">AI Project</a>
                <span class="text-gray-500 text-xs">Started 2 weeks ago</span>
            </div>
            <p class="text-gray-700">This project is about creating an AI that can generate code based on human
                instructions. It is currently in progress.</p>
        </li>
        <li class="px-6 py-4">
            <div class="flex justify-between">
                <a href="./addtask.php" class="font-semibold text-lg">Game Development</a>
                <span class="text-gray-500 text-xs">Started 3 weeks ago</span>
            </div>
            <p class="text-gray-700">This project is about creating a game using Unity. It is currently in progress.</p>
        </li>
    </ul>

</body>

</html>