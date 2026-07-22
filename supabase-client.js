const SUPABASE_URL = 'https://YOUR_PROJECT_REF.supabase.co';
const SUPABASE_ANON_KEY = 'YOUR_ANON_KEY';

let supabaseClient = null;

function getSupabaseClient() {
  if (!supabaseClient && window.supabase) {
    if (
      SUPABASE_URL.includes('YOUR_PROJECT_REF') ||
      SUPABASE_ANON_KEY.includes('YOUR_ANON_KEY')
    ) {
      console.warn('Supabase no está configurado aún. Reemplaza la URL y anon key en supabase-client.js.');
      return null;
    }

    supabaseClient = window.supabase.createClient(SUPABASE_URL, SUPABASE_ANON_KEY);
  }

  return supabaseClient;
}

async function guardarIntentoLogin(usuario) {
  const client = getSupabaseClient();
  if (!client) {
    return false;
  }

  const { error } = await client.from('visits').insert([
    {
      usuario,
      fuente: 'login',
      created_at: new Date().toISOString()
    }
  ]);

  if (error) {
    console.error('No se pudo guardar el acceso:', error);
    return false;
  }

  return true;
}

async function obtenerMenu() {
  const client = getSupabaseClient();
  if (!client) {
    return [];
  }

  const { data, error } = await client
    .from('menu_items')
    .select('id, nombre, precio, descripcion, ubicacion, rating, color')
    .order('id', { ascending: true });

  if (error) {
    console.error('No se pudo cargar el menú desde Supabase:', error);
    return [];
  }

  return data || [];
}

function mostrarEstadoSupabase(texto) {
  const nodo = document.getElementById('supabase-status');
  if (nodo) {
    nodo.textContent = texto;
  }
}

async function cargarMenuDesdeSupabase() {
  const contenedor = document.querySelector('.dishes-grid');
  if (!contenedor) {
    return;
  }

  const items = await obtenerMenu();

  if (!items.length) {
    mostrarEstadoSupabase('Supabase listo, pero aún no hay registros en la tabla menu_items.');
    return;
  }

  contenedor.innerHTML = items
    .map(
      (item) => `
        <article class="dish-card">
          <div class="dish-image" style="background: ${item.color || 'linear-gradient(135deg, #d97706 0%, #9a2c00 100%)'};">
            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 300 200'%3E%3Crect fill='%23ffffff' width='300' height='200'/%3E%3Ctext x='50%25' y='50%25' text-anchor='middle' dy='.3em' fill='%23000000' font-size='22' font-family='sans-serif'%3E${encodeURIComponent(item.ubicacion || 'Supabase')}%3C/text%3E%3C/svg%3E" alt="${item.ubicacion || 'Plato'}" />
          </div>
          <div class="dish-info">
            <div class="dish-header">
              <span class="dish-location">${item.ubicacion || '🌍'} </span>
              <span class="dish-rating">⭐ ${item.rating || '4.8'}</span>
            </div>
            <h3>${item.nombre}</h3>
            <p>${item.descripcion || 'Plato sincronizado desde Supabase.'}</p>
            <div class="dish-footer">
              <span class="dish-price">$${Number(item.precio || 0).toFixed(2)}</span>
              <button class="btn-add-cart" onclick="agregarAlCarrito('${item.nombre}', ${Number(item.precio || 0)})">Agregar</button>
            </div>
          </div>
        </article>
      `
    )
    .join('');

  mostrarEstadoSupabase('Conectado a Supabase y cargando datos del menú.');
}

document.addEventListener('DOMContentLoaded', () => {
  if (document.querySelector('.dishes-grid')) {
    cargarMenuDesdeSupabase();
  }
});
