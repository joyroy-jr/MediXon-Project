// MediXon — Owner Requests Scripts (Equipment + Medicine)

// ── EQUIPMENT REQUESTS ───────────────────────────────────────
function loadOwnerRequests(){
  fetch('getRequests.php').then(r=>r.json()).then(data=>{
    const container=document.getElementById('ownerEquipRequestsList');
    if(!container)return;
    container.innerHTML='';
    if(!Array.isArray(data)||!data.length){
      container.innerHTML='<div class="empty-state"><div style="font-size:2rem;margin-bottom:.5rem;">📋</div><p>No equipment requests received yet.</p></div>';
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
        <p><strong>From:</strong> ${req.requester_name||'User'}</p>
        <p><strong>Message:</strong> ${req.message||'—'}</p>
        <p><strong>Received:</strong> ${new Date(req.created_at).toLocaleDateString('en-BD',{day:'2-digit',month:'short',year:'numeric'})}</p>
        ${req.status==='Pending'?`
        <div class="card-actions" style="margin-top:.9rem;">
          <button class="request-btn" style="background:linear-gradient(135deg,#10b981,#059669);color:#fff;flex:1;" onclick="acceptEquipRequest(${req.id})">✓ Accept</button>
          <button class="request-btn" style="background:linear-gradient(135deg,#ef4444,#dc2626);color:#fff;flex:1;" onclick="rejectEquipRequest(${req.id})">✕ Reject</button>
        </div>`:''}`;
      container.appendChild(card);
    });
  }).catch(e=>console.error('Owner equip requests:',e));
}

function acceptEquipRequest(id){
  if(!confirm('Accept this request? The requester will be notified and a chat will open.'))return;
  const fd=new FormData(); fd.append('requestId',id);
  fetch('acceptRequest.php',{method:'POST',body:fd}).then(r=>r.json()).then(d=>{
    if(d.success){alert('✅ Request accepted! Chat has been initiated.');loadOwnerRequests();}
    else alert('Error: '+(d.error||'Failed'));
  }).catch(e=>console.error(e));
}

function rejectEquipRequest(id){
  if(!confirm('Reject this request?'))return;
  const fd=new FormData(); fd.append('requestId',id);
  fetch('rejectRequest.php',{method:'POST',body:fd}).then(r=>r.json()).then(d=>{
    if(d.success){alert('Request rejected.');loadOwnerRequests();}
    else alert('Error: '+(d.error||'Failed'));
  }).catch(e=>console.error(e));
}

// ── MEDICINE REQUESTS ────────────────────────────────────────
function loadOwnerMedicineRequests(){
  fetch('getMedicineRequests.php').then(r=>r.json()).then(data=>{
    const container=document.getElementById('ownerMedRequestsList');
    if(!container)return;
    container.innerHTML='';
    if(!Array.isArray(data)||!data.length){
      container.innerHTML='<div class="empty-state"><div style="font-size:2rem;margin-bottom:.5rem;">💊</div><p>No medicine requests received yet.</p></div>';
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
        <p><strong>From:</strong> ${req.requester_name||'User'}</p>
        <p><strong>Message:</strong> ${req.message||'—'}</p>
        <p><strong>Received:</strong> ${new Date(req.created_at).toLocaleDateString('en-BD',{day:'2-digit',month:'short',year:'numeric'})}</p>
        ${req.status==='Pending'?`
        <div class="card-actions" style="margin-top:.9rem;">
          <button class="request-btn" style="background:linear-gradient(135deg,#10b981,#059669);color:#fff;flex:1;" onclick="acceptMedRequest(${req.id})">✓ Accept</button>
          <button class="request-btn" style="background:linear-gradient(135deg,#ef4444,#dc2626);color:#fff;flex:1;" onclick="rejectMedRequest(${req.id})">✕ Reject</button>
        </div>`:''}`;
      container.appendChild(card);
    });
  }).catch(e=>console.error('Owner med requests:',e));
}

function acceptMedRequest(id){
  if(!confirm('Accept this medicine request? The requester will be notified and a chat will open.'))return;
  const fd=new FormData(); fd.append('requestId',id);
  fetch('acceptMedicineRequest.php',{method:'POST',body:fd}).then(r=>r.json()).then(d=>{
    if(d.success){alert('✅ Medicine request accepted! Chat has been initiated.');loadOwnerMedicineRequests();}
    else alert('Error: '+(d.error||'Failed'));
  }).catch(e=>console.error(e));
}

function rejectMedRequest(id){
  if(!confirm('Reject this medicine request?'))return;
  const fd=new FormData(); fd.append('requestId',id);
  fetch('rejectMedicineRequest.php',{method:'POST',body:fd}).then(r=>r.json()).then(d=>{
    if(d.success){alert('Medicine request rejected.');loadOwnerMedicineRequests();}
    else alert('Error: '+(d.error||'Failed'));
  }).catch(e=>console.error(e));
}

window.addEventListener('load',()=>{
  if(document.getElementById('ownerEquipRequestsList')) loadOwnerRequests();
});
