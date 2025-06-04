const msgerForm = document.querySelector(".msger-inputarea");
const msgerInput = document.querySelector(".msger-input");
const msgerChat = document.querySelector(".msger-chat");

const BOT_IMG = "bot.png";
const PERSON_IMG = "user.png";
const BOT_NAME = "FAQ Bot";
const PERSON_NAME = "User";

const faqPrompts = [
    ["hi", "hello", "how do you do", "what's up"],
    ["what is this project about", "describe the project", "explain your project"],
    ["what technologies are used", "which tech stack", "what tools did you use"],
    ["who developed this", "who created this project", "who made this"],
    ["how does this work", "how does your system function", "explain the workflow"],
    ["what problem does this solve", "why is this project important", "purpose of this project"],
    ["how can I use this", "how do I access this", "how do I interact with it"],
    ["is this project open-source", "can I contribute", "can I modify this"],
    ["who can benefit from this", "who is this project for", "target audience"],
    ["what future improvements are planned", "any upgrades", "future scope"],
    ["where can I get more information", "is there any documentation", "any resources"],
    ["how does mentor matching work", "how do I find a mentor", "how are mentors assigned"],
    ["how do students get course recommendations", "how is learning path generated", "how do course suggestions work"],
    ["how do placement officers use this", "what features do placement officers have", "how do recruiters interact with this"],
    ["how are job alerts sent", "how do students receive job updates", "how does the placement system notify students"],
    ["how can mentors analyze student performance", "what analytics do mentors see", "how do mentors track progress"],
    ["can students interact with mentors", "is there a chat feature", "how do students ask mentors questions"]
];

const faqReplies = [
    ["Hello! How can I assist you today? 😊"],
    ["This project is an Automated Learning Path and Mentor Matching System designed to provide students with personalized learning paths and mentorship opportunities."],
    ["The project uses Machine Learning, AI, and Web Technologies like PHP, HTML, JS and BootStrap for UI enhancements."],
    ["This project was developed by a team of passionate developers, including Kavya Akanksha Batchu."],
    ["The system helps students get personalized learning paths, matches them with mentors, and assists placement officers in filtering students and posting job opportunities."],
    ["It helps students improve their learning, provides mentorship, and streamlines job placements, making the education system more efficient."],
    ["You can access it via a web application. Users need to sign up, log in, and start exploring courses, finding mentors, or accessing job alerts."],
    ["Yes, it is open-source! You can contribute to our GitHub repository."],
    ["Students, mentors, and placement officers can benefit from this platform. It provides students with guided learning, mentors with engagement opportunities, and placement officers with recruitment tools."],
    ["Future improvements include AI-powered mentor matching enhancements, better learning analytics, and expanded job placement integrations."],
    ["You can find more details in the documentation on our website or GitHub page."],
    ["Mentor matching is based on student input such as course name, difficulty level, and expected mentor rating. The system recommends mentors accordingly."],
    ["Students receive course recommendations by entering their education level, skills, and career goals. The system uses ML algorithms to suggest relevant courses."],
    ["Placement officers can filter students by skills, CGPA, and certifications, post job openings, and track placements."],
    ["Students receive job alerts via email when new opportunities are posted by placement officers."],
    ["Mentors can access student performance data, including study hours, CGPA, and skill progress, using built-in analytics."],
    ["Yes! Students can interact with mentors through the mentor matching system and ask questions directly."]
];

msgerForm.addEventListener("submit", event => {
    event.preventDefault();
    const msgText = msgerInput.value.trim();
    if (!msgText) return;
    addChat(PERSON_NAME, PERSON_IMG, "right", msgText);
    msgerInput.value = "";
    botReply(msgText);
});

function botReply(input) {
    const text = input.toLowerCase();
    const reply = getFAQReply(text) || "I'm sorry, I don't have an answer to that. Try asking something related to the project.";
    const delay = text.split(" ").length * 100;
    
    setTimeout(() => {
        addChat(BOT_NAME, BOT_IMG, "left", reply);
    }, delay);
}

function getFAQReply(input) {
    for (let i = 0; i < faqPrompts.length; i++) {
        if (faqPrompts[i].some(prompt => input.includes(prompt))) {
            return faqReplies[i][Math.floor(Math.random() * faqReplies[i].length)];
        }
    }
    return null;
}

function addChat(name, img, side, text) {
    const msgHTML = `
        <div class="msg ${side}-msg">
            <div class="msg-img" style="background-image: url(${img});"></div>
            <div class="msg-bubble">
                <div class="msg-info">
                    <div class="msg-info-name">${name}</div>
                    <div class="msg-info-time">${formatTime(new Date())}</div>
                </div>
                <div class="msg-text">${text}</div>
            </div>
        </div>
    `;
    msgerChat.insertAdjacentHTML("beforeend", msgHTML);
    msgerChat.scrollTop += 500;
}

function formatTime(date) {
    const h = "0" + date.getHours();
    const m = "0" + date.getMinutes();
    return `${h.slice(-2)}:${m.slice(-2)}`;
}
