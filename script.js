console.log("MediXon Loaded");

// ── BADGE HELPERS ────────────────────────────────────────────
function modeBadge(mode){
  const map={'Donate':'badge-donate','Swap':'badge-swap','Rent':'badge-rent','Low Price Sale':'badge-sale'};
  return map[mode]||'badge-new';
}
function condBadge(c){
  const map={'New':'badge-new','Used':'badge-used','Sealed':'badge-sealed','Opened':'badge-opened','Partial':'badge-partial'};
  return map[c]||'badge-new';
}
function statusBadge(s){
  const map={'Pending':'badge-pending','Accepted':'badge-accepted','Rejected':'badge-rejected'};
  return map[s]||'badge-pending';
}
function esc(t){if(!t)return'';return String(t).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}
function fmtDate(d){return d?new Date(d).toLocaleDateString('en-BD',{day:'2-digit',month:'short',year:'numeric'}):'—';}

// ── DISPLAY EQUIPMENT CARDS ──────────────────────────────────
function displayEquipment(containerId, items){
  const container=document.getElementById(containerId);
  if(!container)return;
  container.innerHTML='';
  if(!items||!items.length){
    container.innerHTML='<div class="empty-state"><div style="font-size:2.5rem;margin-bottom:.75rem;">🩺</div><p>No equipment found.</p><p style="font-size:12px;margin-top:.4rem;">Try adjusting your filters or <a href="uploadEquipment.html" style="color:var(--primary);font-weight:600;">add new equipment</a></p></div>';
    return;
  }
  items.forEach(eq=>{
    const card=document.createElement('div');
    card.className='equipment-card';
    let priceHtml='';
    if(eq.mode==='Rent'&&eq.rent_per_day) priceHtml=`<p><strong>Rent/Day:</strong> <span style="color:var(--orange);font-weight:700;">৳${parseFloat(eq.rent_per_day).toFixed(2)}</span></p>`;
    else if(eq.mode==='Low Price Sale'&&eq.price) priceHtml=`<p><strong>Price:</strong> <span style="color:var(--red);font-weight:700;">৳${parseFloat(eq.price).toFixed(2)}</span></p>`;
    card.innerHTML=`
      <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:.7rem;gap:.5rem;">
        <h3 style="margin:0;font-size:15px;">${esc(eq.name)}</h3>
        <span class="badge ${modeBadge(eq.mode)}" style="white-space:nowrap;">${esc(eq.mode)}</span>
      </div>
      <p><strong>Category:</strong> ${esc(eq.category)}</p>
      ${eq.company?`<p><strong>Brand:</strong> ${esc(eq.company)}</p>`:''}
      <p><strong>Qty:</strong> ${esc(eq.quantity)} &nbsp;|&nbsp; <strong>Condition:</strong> <span class="badge ${condBadge(eq.condition_type)}">${esc(eq.condition_type)}</span></p>
      ${priceHtml}
      ${eq.location?`<p><strong>📍</strong> ${esc(eq.location)}</p>`:''}
      ${eq.owner_name?`<p><strong>Owner:</strong> ${esc(eq.owner_name)}</p>`:''}
      ${eq.photo?`<img src="${esc(eq.photo)}" class="card-img" alt="equipment photo">`:''}
      <div class="card-actions">
        <button class="request-btn" style="background:var(--grad-btn);color:#fff;" onclick="requestEquipment(${eq.id})">📋 Request</button>
      </div>`;
    container.appendChild(card);
  });
}

// ── DISPLAY MEDICINE CARDS ───────────────────────────────────
function displayMedicine(containerId, items){
  const container=document.getElementById(containerId);
  if(!container)return;
  container.innerHTML='';
  if(!items||!items.length){
    container.innerHTML='<div class="empty-state"><div style="font-size:2.5rem;margin-bottom:.75rem;">💊</div><p>No medicines found.</p><p style="font-size:12px;margin-top:.4rem;">Try adjusting your filters or <a href="uploadMedicine.html" style="color:var(--med-teal);font-weight:600;">add new medicine</a></p></div>';
    return;
  }
  items.forEach(med=>{
    const card=document.createElement('div');
    card.className='medicine-card';
    const today=new Date(); const expDate=med.expiry_date?new Date(med.expiry_date):null;
    const isExpiring=expDate&&(expDate-today)<(30*24*60*60*1000)&&expDate>today;
    const isExpired=expDate&&expDate<today;
    let expiryHtml='';
    if(med.expiry_date){
      const cls=isExpired?'expiry-warn':isExpiring?'expiry-warn':'';
      const icon=isExpired?'⛔':isExpiring?'⚠️':'✅';
      expiryHtml=`<p class="${cls}"><strong>Expiry:</strong> ${icon} ${fmtDate(med.expiry_date)}${isExpired?' (EXPIRED)':isExpiring?' (Expiring soon)':''}</p>`;
    }
    let priceHtml='';
    if(med.mode==='Low Price Sale'&&med.price) priceHtml=`<p><strong>Price:</strong> <span style="color:var(--red);font-weight:700;">৳${parseFloat(med.price).toFixed(2)}</span></p>`;
    card.innerHTML=`
      <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:.7rem;gap:.5rem;">
        <div>
          <h3 style="margin:0;font-size:15px;">${esc(med.name)}</h3>
          ${med.generic_name?`<div style="font-size:11.5px;color:var(--text-d3);margin-top:2px;">${esc(med.generic_name)}</div>`:''}
        </div>
        <span class="badge ${modeBadge(med.mode)}" style="white-space:nowrap;">${esc(med.mode)}</span>
      </div>
      <p><strong>Category:</strong> ${esc(med.category)} &nbsp;|&nbsp; <span class="badge badge-tablet">${esc(med.dosage_form)}</span></p>
      ${med.strength?`<p><strong>Strength:</strong> ${esc(med.strength)}</p>`:''}
      ${med.manufacturer?`<p><strong>Brand:</strong> ${esc(med.manufacturer)}</p>`:''}
      <p><strong>Qty:</strong> ${esc(med.quantity)} ${esc(med.unit||'pcs')} &nbsp;|&nbsp; <span class="badge ${condBadge(med.condition_type)}">${esc(med.condition_type)}</span></p>
      ${expiryHtml}
      ${priceHtml}
      ${med.location?`<p><strong>📍</strong> ${esc(med.location)}</p>`:''}
      ${med.owner_name?`<p><strong>Owner:</strong> ${esc(med.owner_name)}</p>`:''}
      ${med.photo?`<img src="${esc(med.photo)}" class="card-img" alt="medicine photo">`:''}
      <div class="card-actions">
        <button class="request-btn" style="background:var(--grad-med);color:#fff;" onclick="requestMedicine(${med.id})"${isExpired?' disabled title="Expired medicine"':''}>💊 Request</button>
      </div>`;
    container.appendChild(card);
  });
}

// ── LOAD DASHBOARD ───────────────────────────────────────────
function loadDashboardData(){
  const s =document.getElementById('searchInput')?.value||'';
  const c =document.getElementById('filterCategory')?.value||'';
  const co=document.getElementById('filterCondition')?.value||'';
  const m =document.getElementById('filterMode')?.value||'';
  const ms=document.getElementById('medSearch')?.value||'';
  const mc=document.getElementById('medCategory')?.value||'';
  const mf=document.getElementById('medForm')?.value||'';
  const mm=document.getElementById('medMode')?.value||'';
  fetch(`dashboardData.php?search=${encodeURIComponent(s)}&category=${encodeURIComponent(c)}&condition=${encodeURIComponent(co)}&mode=${encodeURIComponent(m)}&medSearch=${encodeURIComponent(ms)}&medCategory=${encodeURIComponent(mc)}&medForm=${encodeURIComponent(mf)}&medMode=${encodeURIComponent(mm)}`)
    .then(r=>r.json()).then(data=>{
      if(document.getElementById('statEquip')) document.getElementById('statEquip').textContent=data.equip||0;
      if(document.getElementById('statMed'))   document.getElementById('statMed').textContent=data.meds||0;
      if(document.getElementById('statReq'))   document.getElementById('statReq').textContent=data.requests||0;
      if(document.getElementById('statMsg'))   document.getElementById('statMsg').textContent=data.messages||0;
      if(data.equipment) displayEquipment('equipmentList',data.equipment);
      if(data.medicines) displayMedicine('medicineList',data.medicines);
    }).catch(e=>console.error('Dashboard:',e));
}

// ── LOAD BROWSE EQUIPMENT ────────────────────────────────────
function loadBrowseEquipment(){
  const s =document.getElementById('browseSearch')?.value||'';
  const c =document.getElementById('browseCategory')?.value||'';
  const co=document.getElementById('browseCondition')?.value||'';
  const m =document.getElementById('browseMode')?.value||'';
  fetch(`browseEquipment.php?search=${encodeURIComponent(s)}&category=${encodeURIComponent(c)}&condition=${encodeURIComponent(co)}&mode=${encodeURIComponent(m)}`)
    .then(r=>r.json()).then(data=>displayEquipment('browseList',data))
    .catch(e=>console.error('Browse:',e));
}

// ── LOAD BROWSE MEDICINE ─────────────────────────────────────
function loadBrowseMedicine(){
  const s =document.getElementById('medSearch')?.value||'';
  const c =document.getElementById('medCategory')?.value||'';
  const f =document.getElementById('medForm')?.value||'';
  const m =document.getElementById('medMode')?.value||'';
  fetch(`browseMedicine.php?search=${encodeURIComponent(s)}&category=${encodeURIComponent(c)}&dosage_form=${encodeURIComponent(f)}&mode=${encodeURIComponent(m)}`)
    .then(r=>r.json()).then(data=>displayMedicine('medicineList',data))
    .catch(e=>console.error('Browse Medicine:',e));
}

// ── REQUEST EQUIPMENT ────────────────────────────────────────
function requestEquipment(equipmentId){
  const message=prompt('Write a short message to the owner (optional):');
  if(message===null)return;
  const fd=new FormData();
  fd.append('equipmentId',equipmentId);
  fd.append('message',message||'');
  fetch('requestEquipment.php',{method:'POST',body:fd})
    .then(r=>r.text()).then(res=>{
      const msgs={'SUCCESS':'✅ Request sent successfully!','DUPLICATE':'⚠️ You already sent a request for this item.','OWN_EQUIPMENT':'⚠️ You cannot request your own equipment.','NOT_FOUND':'❌ Equipment not found.','UNAUTHORIZED':'❌ Please log in first.'};
      alert(msgs[res.trim()]||'Something went wrong. Please try again.');
    }).catch(e=>{console.error(e);alert('Network error. Please try again.');});
}

// ── REQUEST MEDICINE ─────────────────────────────────────────
function requestMedicine(medicineId){
  const message=prompt('Write a short message to the owner (optional):');
  if(message===null)return;
  const fd=new FormData();
  fd.append('medicineId',medicineId);
  fd.append('message',message||'');
  fetch('requestMedicine.php',{method:'POST',body:fd})
    .then(r=>r.text()).then(res=>{
      const msgs={'SUCCESS':'✅ Medicine request sent successfully!','DUPLICATE':'⚠️ You already sent a request for this medicine.','OWN_MEDICINE':'⚠️ You cannot request your own medicine.','NOT_FOUND':'❌ Medicine not found.','UNAUTHORIZED':'❌ Please log in first.'};
      alert(msgs[res.trim()]||'Something went wrong. Please try again.');
    }).catch(e=>{console.error(e);alert('Network error. Please try again.');});
}

// ── EVENT LISTENERS ──────────────────────────────────────────
function addEv(id,ev,fn){const el=document.getElementById(id);if(el)el.addEventListener(ev,fn);}
// Equipment filters
addEv('searchInput','input',loadDashboardData);
addEv('filterCategory','change',loadDashboardData);
addEv('filterCondition','change',loadDashboardData);
addEv('filterMode','change',loadDashboardData);
// Medicine filters (dashboard)
addEv('medSearch','input',()=>{loadDashboardData();loadBrowseMedicine();});
addEv('medCategory','change',()=>{loadDashboardData();loadBrowseMedicine();});
addEv('medForm','change',()=>{loadDashboardData();loadBrowseMedicine();});
addEv('medMode','change',()=>{loadDashboardData();loadBrowseMedicine();});
// Browse equipment filters
addEv('browseSearch','input',loadBrowseEquipment);
addEv('browseCategory','change',loadBrowseEquipment);
addEv('browseCondition','change',loadBrowseEquipment);
addEv('browseMode','change',loadBrowseEquipment);

document.addEventListener('DOMContentLoaded',()=>{
  if(document.getElementById('equipmentList')) loadDashboardData();
  if(document.getElementById('browseList'))    loadBrowseEquipment();
  if(document.getElementById('medicineList')&&document.getElementById('medSearch')) loadBrowseMedicine();
});
