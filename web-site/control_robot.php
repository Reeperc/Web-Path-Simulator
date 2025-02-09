<?php
include 'headerDoctor.php';
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Control Robot</h1>

    <div class="control-interface">
        <!-- Joystick Container -->
        <div class="joystick-container">
            <div class="joystick" id="joystick"></div>
        </div>

        <!-- Directional Buttons -->
        <div class="control-panel">
            <button id="up" class="control-button">↑</button>
            <div class="middle-row">
                <button id="left" class="control-button">←</button>
                <button id="stop" class="control-button stop-button">⏹</button>
                <button id="right" class="control-button">→</button>
            </div>
            <button id="down" class="control-button">↓</button>
        </div>
    </div>

    <!-- Robot Playground -->
    <div class="playground">
        <div class="robot" id="robot"></div>
    </div>
</div>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Robot Control with Joystick and Buttons</title>
    <style>
        /* Flexbox for side-by-side layout */
        .control-interface {
            display: flex;
            justify-content: space-around;
            align-items: center;
            margin: 20px auto;
        }

        /* Joystick Styles */
        .joystick-container {
            width: 200px;
            height: 200px;
            background-color: #f0f0f0;
            border: 3px solid #ccc;
            border-radius: 50%;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .joystick {
            width: 60px;
            height: 60px;
            background-color: #1cc88a;
            border: 2px solid #17a673;
            border-radius: 50%;
            position: absolute;
            cursor: grab;
        }

        /* Playground Styles */
        .playground {
            margin: 30px auto;
            width: 400px;
            height: 400px;
            border: 2px solid #ccc;
            background-color: #fff;
            position: relative;
        }

        .robot {
            width: 50px;
            height: 50px;
            background-color: #1cc88a;
            border-radius: 8px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        /* Button Panel Styles */
        .control-panel {
            display: grid;
            grid-template-columns: 100px 100px 100px;
            grid-gap: 10px;
            justify-content: center;
            align-items: center;
        }

        .control-button {
            width: 100px;
            height: 100px;
            font-size: 24px;
            background-color: #1cc88a;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.2s ease-in-out;
        }

        .control-button:hover {
            transform: scale(1.1);
            background-color: #17a673;
        }

        .stop-button {
            background-color: #e74a3b;
        }

        .stop-button:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>

<script>
    // Joystick Movement Logic
    const joystick = document.getElementById('joystick');
    const container = joystick.parentElement;
    const robot = document.getElementById('robot');

    let isDragging = false;
    let joystickX = 0, joystickY = 0;

    joystick.addEventListener('mousedown', (e) => {
        isDragging = true;
        e.preventDefault();
    });

    window.addEventListener('mousemove', (e) => {
        if (!isDragging) return;

        const rect = container.getBoundingClientRect();
        const centerX = rect.width / 2;
        const centerY = rect.height / 2;

        const mouseX = e.clientX - rect.left - centerX;
        const mouseY = e.clientY - rect.top - centerY;

        const distance = Math.min(Math.sqrt(mouseX ** 2 + mouseY ** 2), centerX - joystick.offsetWidth / 2);
        const angle = Math.atan2(mouseY, mouseX);

        joystickX = distance * Math.cos(angle);
        joystickY = distance * Math.sin(angle);

        joystick.style.transform = `translate(${joystickX}px, ${joystickY}px)`;

        // Move robot in the same direction as the joystick
        moveRobot(joystickX / centerX * 5, joystickY / centerY * 5);
    });

    window.addEventListener('mouseup', () => {
        isDragging = false;
        joystickX = 0;
        joystickY = 0;
        joystick.style.transform = 'translate(0, 0)';
    });

    // Robot Movement Variables
    let posX = 50; // Start at center (percentage)
    let posY = 50;

    // Add event listeners to directional buttons
    document.getElementById('up').addEventListener('click', () => moveRobot(0, -5));
    document.getElementById('down').addEventListener('click', () => moveRobot(0, 5));
    document.getElementById('left').addEventListener('click', () => moveRobot(-5, 0));
    document.getElementById('right').addEventListener('click', () => moveRobot(5, 0));
    document.getElementById('stop').addEventListener('click', stopRobot);

    // Move robot function
    function moveRobot(dx, dy) {
        posX += dx;
        posY += dy;

        // Ensure robot stays inside the playground
        posX = Math.max(0, Math.min(100, posX));
        posY = Math.max(0, Math.min(100, posY));

        // Update robot position
        robot.style.left = posX + '%';
        robot.style.top = posY + '%';
    }

    // Stop robot function
    function stopRobot() {
        alert('Robot stopped!');
    }
</script>

</body>
</html>

<?php
include 'footerDoctor.php';
?>
