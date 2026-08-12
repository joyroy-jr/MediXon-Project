console.log("MediCycle Chat Loaded");
let currentChatUserId = null;
let currentChatName   = null;
let msgInterval       = null;

function loadChatUsers() {
  fetch("getChatUsers.php").then(r=>r.json()).then(data=>{
    const list = document.getElementById("chatUsers");
    list.innerHTML = '<div class="chat-users-header">Your Chats</div>';
    if (!data.length) {
      list.innerHTML += '<div style="padding:1rem 1.2rem;font-size:13px;color:var(--text-w3);">No chats yet. Accept a request to start.</div>';
      return;
    }
    data.forEach(u => {
      const div = document.createElement("div");
      div.className = "chat-user";
      if (currentChatUserId == u.id) div.classList.add("active");
      const initial = (u.name && u.name.length) ? u.name.charAt(0).toUpperCase() : "?";
      const picHtml = u.profile_pic
        ? `<div class="chat-avatar"><img src="${u.profile_pic}" alt="${initial}"></div>`
        : `<div class="chat-avatar">${initial}</div>`;
      div.innerHTML = `${picHtml}<span class="chat-user-name">${u.name}</span><span class="online-dot"></span>`;
      div.onclick = () => openChat(u.id, u.name, u.profile_pic);
      list.appendChild(div);
    });
  }).catch(e=>console.error("Chat users:",e));
}

function openChat(userId, name, pic) {
  currentChatUserId = userId;
  currentChatName   = name;
  document.querySelectorAll(".chat-user").forEach(el=>el.classList.remove("active"));
  event.currentTarget && event.currentTarget.classList.add("active");
  const initial = name.charAt(0).toUpperCase();
  const picHtml = pic
    ? `<div class="chat-avatar" style="width:32px;height:32px;font-size:13px;"><img src="${pic}" alt="${initial}"></div>`
    : `<div class="chat-avatar" style="width:32px;height:32px;font-size:13px;">${initial}</div>`;
  document.getElementById("chatHeader").innerHTML = `${picHtml} ${name}`;
  loadMessages();
  if (msgInterval) clearInterval(msgInterval);
  msgInterval = setInterval(loadMessages, 2500);
}

function loadMessages() {
  if (!currentChatUserId) return;
  fetch("loadMessages.php?userId=" + currentChatUserId).then(r=>r.json()).then(data=>{
    const area = document.getElementById("messagesArea");
    const atBottom = area.scrollHeight - area.scrollTop <= area.clientHeight + 60;
    area.innerHTML = "";
    if (!data.length) {
      area.innerHTML = '<div style="text-align:center;color:var(--text-d3);margin:auto;font-size:13px;padding:2rem;">No messages yet. Say hello!</div>';
      return;
    }
    data.forEach(msg => {
      const div = document.createElement("div");
      div.className = "message " + (msg.me ? "me" : "them");
      let content = "";
      if (msg.text) content += `<span>${escHtml(msg.text)}</span>`;
      if (msg.attachment) {
        if (msg.attachType && msg.attachType.startsWith("image/"))
          content += `<img src="${msg.attachment}" alt="image">`;
        else
          content += `<a href="${msg.attachment}" target="_blank" style="color:inherit;text-decoration:underline;display:block;margin-top:4px;">📎 View Attachment</a>`;
      }
      div.innerHTML = content;
      area.appendChild(div);
    });
    if (atBottom) area.scrollTop = area.scrollHeight;
  }).catch(e=>console.error("Messages:",e));
}

document.getElementById("sendBtn").addEventListener("click", sendMsg);
document.getElementById("msgBox").addEventListener("keydown", e=>{ if(e.key==="Enter") sendMsg(); });

function sendMsg() {
  const text = document.getElementById("msgBox").value.trim();
  if (!text || !currentChatUserId) return;
  const fd = new FormData();
  fd.append("receiver", currentChatUserId);
  fd.append("text", text);
  fetch("sendMessage.php",{method:"POST",body:fd}).then(()=>{
    document.getElementById("msgBox").value="";
    loadMessages();
  }).catch(e=>console.error("Send:",e));
}

document.getElementById("attachBtn").addEventListener("click",()=>{
  if (!currentChatUserId) { alert("Please select a chat first."); return; }
  document.getElementById("fileInput").click();
});

document.getElementById("fileInput").addEventListener("change", function(){
  const file = this.files[0];
  if (!file || !currentChatUserId) return;
  const fd = new FormData();
  fd.append("receiver", currentChatUserId);
  fd.append("attachment", file);
  fetch("sendMessage.php",{method:"POST",body:fd}).then(()=>{
    this.value="";
    loadMessages();
  }).catch(e=>console.error("Attach:",e));
});

function escHtml(t) { return t.replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;"); }
window.onload = loadChatUsers;
