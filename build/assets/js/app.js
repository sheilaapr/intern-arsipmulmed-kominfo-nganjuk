(()=>{const b=document.body;
document.querySelectorAll('[data-sidebar-open]').forEach(x=>x.onclick=()=>b.classList.add('sidebar-open'));
document.querySelectorAll('[data-sidebar-close]').forEach(x=>x.onclick=()=>b.classList.remove('sidebar-open'));
window.openModal=id=>{const m=document.getElementById(id);if(!m)return;m.classList.add('is-open');b.classList.add('modal-open');setTimeout(()=>m.querySelector('input:not([type=hidden]),textarea,select')?.focus(),30)};
window.closeModal=id=>{const m=document.getElementById(id);if(!m)return;m.classList.remove('is-open');if(!document.querySelector('.modal.is-open'))b.classList.remove('modal-open')};
document.querySelectorAll('[data-modal-open]').forEach(x=>x.onclick=()=>openModal(x.dataset.modalOpen));
document.querySelectorAll('[data-modal-close]').forEach(x=>x.onclick=()=>closeModal(x.closest('.modal')?.id));
document.querySelectorAll('.modal').forEach(m=>m.onmousedown=e=>{if(e.target===m)closeModal(m.id)});
document.querySelectorAll('[data-confirm]').forEach(x=>x.addEventListener('click',e=>{if(!confirm(x.dataset.confirm||'Lanjutkan tindakan ini?'))e.preventDefault()}));
document.querySelectorAll('[data-password-toggle]').forEach(x=>x.onclick=()=>{const t=document.getElementById(x.dataset.passwordToggle);if(!t)return;t.type=t.type==='password'?'text':'password';x.innerHTML=t.type==='password'?'<i class="fa-regular fa-eye"></i>':'<i class="fa-regular fa-eye-slash"></i>'});
document.querySelectorAll('[data-live-search]').forEach(x=>x.oninput=()=>{const q=x.value.trim().toLowerCase();let n=0;document.querySelectorAll(x.dataset.liveSearch).forEach(i=>{const ok=(i.dataset.search||i.textContent).toLowerCase().includes(q);i.hidden=!ok;if(ok)n++});const e=document.querySelector(x.dataset.emptyTarget||'');if(e)e.hidden=n!==0});
document.querySelectorAll('[data-file-input]').forEach(x=>x.onchange=()=>{const t=document.getElementById(x.dataset.fileInput);if(!t)return;t.innerHTML='';[...x.files].slice(0,12).forEach(f=>{const d=document.createElement('div');d.className='upload-preview-item';if(f.type.startsWith('image/')){const i=document.createElement('img');i.src=URL.createObjectURL(f);i.onload=()=>URL.revokeObjectURL(i.src);d.appendChild(i)}else d.innerHTML='<i class="fa-regular fa-file-lines"></i>';const s=document.createElement('span');s.textContent=f.name;d.appendChild(s);t.appendChild(d)})});
document.querySelectorAll('form[data-submit-lock]').forEach(f=>f.onsubmit=()=>{const x=f.querySelector('[type=submit]');if(x){x.disabled=true;x.innerHTML='<i class="fa-solid fa-spinner fa-spin"></i> Memproses...'}});
const toast=document.querySelector('[data-toast]');if(toast)setTimeout(()=>toast.classList.add('toast-hide'),4500);
document.querySelectorAll('[data-toast-close]').forEach(x=>x.onclick=()=>x.closest('[data-toast]')?.remove());
document.addEventListener('keydown',e=>{if(e.key==='Escape'){document.querySelector('.modal.is-open')&&closeModal(document.querySelector('.modal.is-open').id);b.classList.remove('sidebar-open')}})
})();