<?php
session_start();
include('includes/header.php');
include('includes/navbar.php');
?>

<head>
    <link href="TESTBLOG.css" rel="stylesheet">
</head>

<body>
    <div class="mainbody">
        <div class="header-post">
            <h1>Social Platform</h1>
            <form class="post-form">
                <textarea id="postContent" placeholder="Share your experience here ..."></textarea>
                <button type="submit">Post</button>
            </form>
        </div>
        <div class="main-content"></div>
    </div>
</body>

<script src="https://www.gstatic.com/firebasejs/9.6.3/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.6.3/firebase-database.js"></script>
<script>
    // Srcipt Navbar
    // Brandtext
    document.addEventListener('DOMContentLoaded', function AppearOnce() {
        const brandtext = "ChessMate";
        const brandElement = document.getElementById('brand-text');

        function type(index) {
            brandElement.textContent = brandtext.slice(0, index);
            if (index <= brandtext.length)
                setTimeout(() => type(index + 1), 500);
            else {
                setTimeout(() => {
                    document.removeEventListener('DOMContentLoaded', AppearOnce);
                    brandElement.textContent = brandtext;
                }, 1000);
            }
        }
        type(0);
    });

    // Hambuger
    document.addEventListener('DOMContentLoaded', function() {
        const hamburgerIcon = document.querySelector('.hamburger');
        const navLink = document.querySelector('.nav-link');
        const navItems = document.querySelectorAll('.nav-link li a');
        const loginBtn = document.querySelector('.login-btn');

        hamburgerIcon.addEventListener('click', function() {
            navLink.classList.toggle('show');
            if (navLink.classList.contains('show')) {
                navItems.forEach(item => item.style.display = 'block');
                loginBtn.style.display = 'block';
            } else {
                navItems.forEach(item => item.style.display = 'none');
                loginBtn.style.display = 'none';
            }
        });

        window.addEventListener('resize', function() {
            if (window.innerWidth > 1000) {
                navItems.forEach(item => item.style.display = 'block');
                loginBtn.style.display = 'block';
            } else {
                navItems.forEach(item => item.style.display = 'none');
                loginBtn.style.display = 'none';
            }
        });
    });

    const firebaseConfig = {
        apiKey: "",
        authDomain: "",
        databaseURL: "",
        projectId: "",
        storageBucket: "",
        messagingSenderId: "",
        appId: ""
    };

    // Initialize Firebase
    const app = firebase.initializeApp(firebaseConfig);
    const database = app.database();

    // Post
    const form = document.querySelector('.post-form');
    const textarea = document.getElementById('postContent');
    const posts = document.querySelector('.main-content');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const postContent = textarea.value.trim();
        if (postContent !== '') {
            // Thêm post vào Firebase
            database.ref('posts').push({
                    content: postContent,
                    timestamp: firebase.database.ServerValue.TIMESTAMP // Thời gian đăng
                })
                .then(function() {
                    console.log("Posted successfully!");
                    textarea.value = '';
                })
                .catch(function(error) {
                    console.error("Post Error", error);
                });
        }
    });

    // Lấy post từ Firebase
    database.ref('posts').on('child_added', function(snapshot) {
        const postData = snapshot.val();
        const post = document.createElement('div');
        post.classList.add('post');
        post.setAttribute('data-timestamp', postData.timestamp);
        post.innerHTML = `
        <img src="https://picsum.photos/100" alt="Profile Picture">
        <div class="post-header">
            <h2>Username</h2>
            <p>Just now</p>
        </div>
        <p>${postData.content}</p>
        <div class="post-actions">
            <button class="like-button">Like</button>
            <button class="comment-button">Comment</button>
        </div>
        <div class="post-likes">
            <p>0 likes</p>
        </div>
        <div class="post-comments">
            <form class="comment-form">
                <textarea placeholder="Add a comment"></textarea>
                <button>Post</button>
            </form>
        </div>
    `;
        posts.insertBefore(post, posts.firstChild);
    });

    // Interact
    posts.addEventListener('click', function(e) {
        if (e.target.classList.contains('like-button')) {
            const likes = e.target.parentElement.nextElementSibling;
            const isLiked = e.target.classList.contains('liked');
            if (!isLiked) {
                e.target.classList.add('liked');
                likes.innerHTML = `<p>${parseInt(likes.textContent) + 1} likes</p>`;
            } else {
                e.target.classList.remove('liked');
                likes.innerHTML = `<p>${parseInt(likes.textContent) - 1} likes</p>`;
            }
        }
        if (e.target.classList.contains('comment-button')) {
            const commentForm = e.target.parentElement.nextElementSibling.nextElementSibling;
            commentForm.style.display = commentForm.style.display === 'none' ? 'block' : 'none';
        }
        if (e.target.tagName === 'BUTTON') {
            const form = e.target.closest('.comment-form');
            if (form) {
                e.preventDefault();
                const textarea = form.querySelector('textarea');
                const commentContent = textarea.value.trim();
                if (commentContent !== '') {
                    const post = e.target.closest('.post');
                    const commentsSection = post.querySelector('.post-comments');
                    const comment = document.createElement('div');
                    comment.setAttribute('data-timestamp', Date.now());
                    comment.classList.add('comment');
                    comment.innerHTML = `
                    <img src="https://via.placeholder.com/50x50" alt="Profile Picture">
                    <div class="comment-header">
                        <h3>Username</h3>
                        <p>Just now</p>
                    </div>
                    <p class="comment-body">${commentContent}</p>
                    <button class="delete-comment">Delete</button>
                `;
                    commentsSection.insertBefore(comment, commentsSection.firstChild);
                    textarea.value = '';
                }
            }
        }
    });

    const deleteCommentButtons = document.querySelectorAll('.delete-comment');
    deleteCommentButtons.forEach(button => {
        button.addEventListener('click', event => {
            const comment = event.target.parentNode;
            comment.style.display = 'none';
        });
    });

    // Time format
    function formatTimeAgo(timestamp) {
        const postDate = new Date(timestamp);
        const now = new Date();
        const diffSeconds = Math.floor((now - postDate) / 1000);
        if (diffSeconds < 60) {
            return "Just now";
        } else if (diffSeconds < 3600) {
            const diffMinutes = Math.floor(diffSeconds / 60);
            return `${diffMinutes} minute${diffMinutes !== 1 ? 's' : ''} ago`;
        } else if (diffSeconds < 86400) {
            const diffHours = Math.floor(diffSeconds / 3600);
            return `${diffHours} hour${diffHours !== 1 ? 's' : ''} ago`;
        } else if (diffSeconds < 604800) {
            const diffDays = Math.floor(diffSeconds / 86400);
            return `${diffDays} day${diffDays !== 1 ? 's' : ''} ago`;
        } else {
            return postDate.toLocaleDateString();
        }
    }

    function updatePostTimestamps() {
        const posts = document.querySelectorAll('.post');
        posts.forEach(post => {
            const timestamp = parseInt(post.getAttribute('data-timestamp'));
            const timeAgo = formatTimeAgo(timestamp);
            post.querySelector('.post-header p').textContent = timeAgo;
        });
    }
    setInterval(updatePostTimestamps, 60000); // Cập nhật thời gian đăng bài mỗi phút

    function updateCommentTimestamps() {
        const commentsSection = document.querySelectorAll('.post-comments');
        commentsSection.forEach(section => {
            const comments = section.querySelectorAll('.comment');
            comments.forEach(comment => {
                const timestamp = parseInt(comment.getAttribute('data-timestamp'));
                const timeAgo = formatTimeAgo(timestamp);
                comment.querySelector('.comment-header p').textContent = timeAgo;
            });
        });
    }
    setInterval(updateCommentTimestamps, 60000); // Cập nhật thời gian comment mỗi phút
</script>

</html>