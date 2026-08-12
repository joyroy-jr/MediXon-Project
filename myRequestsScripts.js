// MediXon — My Requests Scripts (Equipment + Medicine)

// ── EQUIPMENT REQUESTS ───────────────────────────────────────
function loadMyRequests(){
  fetch('getMyRequests.php').then(r=>r.json()).then(data=>{
    const container=document.getElementById('myEquipRequestsList');
    if(!container)return;
    container.innerHTML='';
    if(!Array.isArray(data)||!data.length){
      container.innerHTML='<div class="empty-state"><div style="font-size:2rem;margin-bottom:.5rem;">📋</div><p>No equipment requests sent yet.</p><p style="font-size:12px;margin-top:.4rem;"><a href="browse.html" style="color:var(--primary);font-weight:600;">Browse equipment to send a request</a></p></div>';
      return;
    }
    data.forEach(req=>{
      const card=document.createElement('div');
      card.className='equipment-card';
      const statusCls={'Accepted':'badge-accepted','Rejected':'badge-rejected','Pending':'badge-pending'}[req.status]||'badge-pending';
      card.innerHTML=`
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:.9rem;gap:.5rem;">
          <h3 style="margin:0;">${req.equipment_name||'Equipment'}</h3>
          <span class="badge ${statusCls}">${req.status}</span>
        </div>
        <p><strong>Message sent:</strong> ${req.message||'—'}</p>
        <p><strong>Requested:</strong> ${new Date(req.created_at).toLocaleDateString('en-BD',{day:'2-digit',month:'short',year:'numeric'})}</p>
        ${req.status==='Accepted'?`<div class="card-actions" style="margin-top:.9rem;"><a href="chat.html" class="request-btn" style="background:var(--grad-btn);color:#fff;text-decoration:none;display:flex;align-items:center;justify-content:center;flex:1;">💬 Open Chat</a></div>`:''}`;
      container.appendChild(card);
    });
  }).catch(e=>console.error('Load my equip requests:',e));
}

// ── MEDICINE REQUESTS ────────────────────────────────────────
function loadMyMedicineRequests(){
  fetch('getMyMedicineRequests.php').then(r=>r.json()).then(data=>{
    const container=document.getElementById('myMedRequestsList');
    if(!container)return;
    container.innerHTML='';
    if(!Array.isArray(data)||!data.length){
      container.innerHTML='<div class="empty-state"><div style="font-size:2rem;margin-bottom:.5rem;">💊</div><p>No medicine requests sent yet.</p><p style="font-size:12px;margin-top:.4rem;"><a href="browseMedicine.html" style="color:var(--med-teal);font-weight:600;">Browse medicines to send a request</a></p></div>';
      return;
    }
    data.forEach(req=>{
      const card=document.createElement('div');
      card.className='medicine-card';
      const statusCls={'Accepted':'badge-accepted','Rejected':'badge-rejected','Pending':'badge-pending'}[req.status]||'badge-pending';
      card.innerHTML=`
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:.9rem;gap:.5rem;">
          <h3 style="margin:0;">💊 ${req.medicine_name||'Medicine'}</h3>
          <span class="badge ${statusCls}">${req.status}</span>
        </div>
        <p><strong>Message sent:</strong> ${req.message||'—'}</p>
        <p><strong>Requested:</strong> ${new Date(req.created_at).toLocaleDateString('en-BD',{day:'2-digit',month:'short',year:'numeric'})}</p>
        ${req.status==='Accepted'?`<div class="card-actions" style="margin-top:.9rem;"><a href="chat.html" class="request-btn" style="background:var(--grad-med);color:#fff;text-decoration:none;display:flex;align-items:center;justify-content:center;flex:1;">💬 Open Chat</a></div>`:''}`;
      container.appendChild(card);
    });
  }).catch(e=>console.error('Load my med requests:',e));
}

window.addEventListener('load',()=>{
  if(document.getElementById('myEquipRequestsList')) loadMyRequests();
});
