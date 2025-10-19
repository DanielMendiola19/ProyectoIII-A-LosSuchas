@extends('layouts.app')
 
@section('title', 'Soporte del Sistema - Coffeeology')
 
@section('content')
<style>
    /* RESET */
* { margin: 0; padding: 0; box-sizing: border-box; }

body {
  font-family: 'Montserrat', sans-serif;
  display: flex;
  min-height: 100vh;
  background: linear-gradient(135deg, #0D0D0D, #1a1a1a, #0D0D0D);
  color: #FAF9F6;
}

/* CONTENIDO */
.content {
  flex: 1;
  margin-left: 220px;
  padding: 40px;
  overflow-y: auto;
  width: calc(100% - 220px);
  animation: fadeIn 1.2s ease-in;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

header {
  text-align: center;
  margin-bottom: 50px;
}

header h1 {
  font-family: 'Playfair Display', serif;
  font-size: 3rem;
  color: #E6B325;
  text-shadow: 0 0 15px rgba(230,179,37,0.5);
  letter-spacing: 1px;
  transition: text-shadow 0.3s ease;
}

header h1:hover {
  text-shadow: 0 0 25px rgba(230,179,37,0.8);
}

header p {
  font-size: 1.2rem;
  color: #C0C0C0;
  margin-top: 5px;
}

.section {
  background: #1a1a1a;
  padding: 30px;
  border-radius: 18px;
  margin-bottom: 45px;
  box-shadow: 0 6px 25px rgba(0,0,0,0.7);
  border: 2px solid #4B2E2E;
  transition: transform 0.3s ease, border-color 0.3s ease;
}

.section:hover {
  transform: translateY(-5px);
  border-color: #E6B325;
}

.section h2 {
  font-family: 'Playfair Display', serif;
  font-size: 1.9rem;
  margin-bottom: 15px;
  color: #E6B325;
  text-shadow: 0 0 8px rgba(230,179,37,0.5);
}

.section p {
  font-family: 'Lora', serif;
  line-height: 1.7;
  color: #FAF9F6;
  font-size: 1.05rem;
}

/* EQUIPO DE DESARROLLO */
.team-photo {
  width: 100%;
  max-width: 800px;
  height: auto;
  background-color: #0D0D0D;
  border: 2px dashed #4B2E2E;
  border-radius: 15px;
  margin: 0 auto 30px auto;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #C0C0C0;
  font-family: 'Lora', serif;
  font-style: italic;
  font-size: 1.1rem;
  overflow: hidden;
  position: relative;
  box-shadow: 0 6px 25px rgba(0,0,0,0.7);
}

.team-photo img {
  width: 100%;
  height: auto;
  border-radius: 15px;
  transition: transform 0.4s ease, filter 0.4s ease;
  object-fit: cover;
}

.team-photo img:hover {
  transform: scale(1.03);
  filter: brightness(1.15);
}

.team {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 25px;
  margin-top: 20px;
}

.team-card {
  background: #0D0D0D;
  padding: 25px;
  border-radius: 15px;
  text-align: center;
  font-family: 'Lora', serif;
  font-size: 1.2rem;
  font-weight: bold;
  color: #FAF9F6;
  box-shadow: 0 4px 20px rgba(0,0,0,0.6);
  cursor: pointer;
  transition: transform 0.3s ease, box-shadow 0.3s ease, border 0.3s ease;
  border: 2px solid #4B2E2E;
  position: relative;
  overflow: hidden;
}

.team-card::after {
  content: "";
  position: absolute;
  top: 0; left: -100%;
  width: 200%; height: 100%;
  background: linear-gradient(120deg, transparent, rgba(230,179,37,0.2), transparent);
  transition: all 0.6s;
}

.team-card:hover::after { left: 100%; }

.team-card:hover {
  transform: translateY(-7px);
  box-shadow: 0 8px 30px rgba(230,179,37,0.4);
  border-color: #E6B325;
}

.team-card span {
  display: block;
  margin-top: 8px;
  font-size: 1rem;
  color: #E6B325;
  font-weight: normal;
}

/* FOOTER */
footer {
  text-align: center;
  padding: 15px;
  margin-top: 40px;
  font-size: 0.9rem;
  color: #C0C0C0;
  border-top: 1px solid #4B2E2E;
  letter-spacing: 0.5px;
}

footer:hover {
  color: #E6B325;
  transition: color 0.3s ease;
}

/* ==================== */
/* RESPONSIVE DESIGN */
/* ==================== */

/* Tablets grandes (1024px - 1199px) */
@media (max-width: 1199px) and (min-width: 1024px) {
  .content {
    margin-left: 200px;
    width: calc(100% - 200px);
    padding: 35px;
  }
  
  header h1 {
    font-size: 2.7rem;
  }
  
  .section {
    padding: 25px;
  }
  
  .team {
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
  }
}

/* Tablets medianas (768px - 1023px) */
@media (max-width: 1023px) and (min-width: 768px) {
  .content {
    margin-left: 180px;
    width: calc(100% - 180px);
    padding: 30px;
  }
  
  header {
    margin-bottom: 40px;
  }
  
  header h1 {
    font-size: 2.4rem;
  }
  
  .section {
    padding: 25px;
    margin-bottom: 35px;
  }
  
  .section h2 {
    font-size: 1.7rem;
  }
  
  .team {
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
  }
  
  .team-card {
    padding: 20px;
    font-size: 1.1rem;
  }
}

/* Tablets pequeñas y móviles grandes (576px - 767px) */
@media (max-width: 767px) and (min-width: 576px) {
  .content {
    margin-left: 0;
    width: 100%;
    padding: 25px 20px;
  }
  
  header {
    margin-bottom: 35px;
  }
  
  header h1 {
    font-size: 2.1rem;
  }
  
  .section {
    padding: 20px;
    margin-bottom: 30px;
    border-radius: 15px;
  }
  
  .section h2 {
    font-size: 1.5rem;
  }
  
  .section p {
    font-size: 1rem;
    line-height: 1.6;
  }
  
  .team-photo {
    margin-bottom: 25px;
    border-radius: 12px;
  }
  
  .team {
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
    margin-top: 15px;
  }
  
  .team-card {
    padding: 18px 15px;
    font-size: 1rem;
    border-radius: 12px;
  }
  
  .team-card span {
    font-size: 0.9rem;
  }
  
  footer {
    margin-top: 30px;
    padding: 12px;
    font-size: 0.85rem;
  }
}

/* Móviles pequeños (hasta 575px) */
@media (max-width: 575px) {
  .content {
    margin-left: 0;
    width: 100%;
    padding: 20px 15px;
  }
  
  header {
    margin-bottom: 30px;
  }
  
  header h1 {
    font-size: 1.8rem;
    letter-spacing: 0.5px;
  }
  
  header p {
    font-size: 1rem;
  }
  
  .section {
    padding: 18px 15px;
    margin-bottom: 25px;
    border-radius: 12px;
    border-width: 1px;
  }
  
  .section h2 {
    font-size: 1.3rem;
    margin-bottom: 12px;
  }
  
  .section p {
    font-size: 0.95rem;
    line-height: 1.5;
  }
  
  .team-photo {
    margin-bottom: 20px;
    border-radius: 10px;
    border-width: 1px;
  }
  
  .team {
    grid-template-columns: 1fr;
    gap: 12px;
    margin-top: 12px;
  }
  
  .team-card {
    padding: 16px 12px;
    font-size: 0.95rem;
    border-radius: 10px;
  }
  
  .team-card span {
    font-size: 0.85rem;
    margin-top: 6px;
  }
  
  footer {
    margin-top: 25px;
    padding: 10px;
    font-size: 0.8rem;
  }
}

/* Móviles muy pequeños (hasta 360px) */
@media (max-width: 360px) {
  .content {
    padding: 15px 10px;
  }
  
  header h1 {
    font-size: 1.6rem;
  }
  
  .section {
    padding: 15px 12px;
    margin-bottom: 20px;
  }
  
  .section h2 {
    font-size: 1.2rem;
  }
  
  .section p {
    font-size: 0.9rem;
  }
  
  .team-card {
    padding: 14px 10px;
    font-size: 0.9rem;
  }
  
  .team-card span {
    font-size: 0.8rem;
  }
  
  footer {
    font-size: 0.75rem;
    padding: 8px;
  }
}

/* Ajustes para orientación landscape en móviles */
@media (max-height: 500px) and (orientation: landscape) {
  .content {
    padding: 15px 20px;
  }
  
  header {
    margin-bottom: 20px;
  }
  
  .section {
    padding: 15px;
    margin-bottom: 20px;
  }
  
  .team {
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 10px;
  }
}

/* Mejoras de accesibilidad para reducir movimiento */
@media (prefers-reduced-motion: reduce) {
  .content {
    animation: none;
  }
  
  .team-card,
  .section,
  header h1 {
    transition: none;
  }
  
  .team-card::after {
    display: none;
  }
}

/* Soporte para modo oscuro del sistema */
@media (prefers-color-scheme: dark) {
  .section {
    background: #1a1a1a;
  }
  
  .team-card {
    background: #0D0D0D;
  }
}

</style>
 
<!-- CONTENIDO -->
<div class="content">
<header>
<h1><i class="bi bi-info-circle"></i> Soporte del Sistema</h1>
</header>
 
  <section class="section">
<h2>Soporte Técnico</h2>
<p><strong>Correo:</strong> zleandro067@gmail.com</p>
<p><strong>Teléfono:</strong> +591 70548255</p>
<p><strong>Horario:</strong> Lunes a Viernes, 9:00 - 18:00</p>
</section>
 
  <section class="section">
<h2>Equipo de Desarrollo</h2>
<p><strong>Universidad del Valle - Proyecto de Sistemas I</strong></p>
 
    <div class="team-photo">
      <img src="img/Grupo.png" alt="Equipo de desarrollo Coffeeology">
</div>
 
    <div class="team">

<div class="team-card">👨‍💻 Alfredo<span>Los Suchas</span></div>
<div class="team-card">👨‍💻 Leandro<span>Los Suchas</span></div>
<div class="team-card">👨‍💻 Alejandro<span>Los Suchas</span></div>
<div class="team-card">👨‍💻 Daniel<span>Los Suchas</span></div>
</div>
</section>
 
  <footer>
&copy; 2025 Coffeeology - Todos los derechos reservados.
</footer>
</div>
@endsection