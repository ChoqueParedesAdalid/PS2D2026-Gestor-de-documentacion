import DocenteLayout from '../../Components/DocenteLayout'
import { useState } from 'react'

export default function GestionTareas({ tareas }) {
    const [mostrarForm, setMostrarForm] = useState(false)
    const [expandida, setExpandida] = useState(null)
    const [nuevaTarea, setNuevaTarea] = useState({ nombre: '', paralelo: '', fecha: '' })

    const entregados = {
        1: ['María Mamani', 'Juan Quispe', 'Ana Ortiz'],
        2: [],
    }

    return (
        <DocenteLayout active="GESTIONAR TAREAS">
            <h1 style={{ fontSize: '28px', fontWeight: 'bold', marginBottom: '4px' }}>GESTIONAR TAREAS</h1>
            <div style={{ color: '#aaa', fontSize: '13px', marginBottom: '24px' }}>Talleres | Paralelo A - Gestión 2027-I</div>

            {/* Botón nueva tarea */}
            <div style={{ marginBottom: '20px' }}>
                <button onClick={() => setMostrarForm(!mostrarForm)}
                    style={{ background: '#e05555', border: 'none', color: 'white', padding: '10px 20px', borderRadius: '6px', cursor: 'pointer', fontSize: '13px', fontWeight: '600' }}>
                    + NUEVA TAREA
                </button>
            </div>

            {/* Form nueva tarea */}
            {mostrarForm && (
                <div style={{ background: '#2d0f0f', borderRadius: '12px', padding: '24px', border: '1px solid #4a1a1a', marginBottom: '24px' }}>
                    <h3 style={{ marginBottom: '16px' }}>Crear nueva tarea</h3>
                    <div style={{ display: 'flex', flexDirection: 'column', gap: '12px' }}>
                        <div>
                            <label style={{ fontSize: '12px', color: '#aaa', display: 'block', marginBottom: '4px' }}>Nombre de la tarea</label>
                            <input value={nuevaTarea.nombre} onChange={e => setNuevaTarea({ ...nuevaTarea, nombre: e.target.value })}
                                placeholder="Ej: Revisión Documental #3"
                                style={{ width: '100%', background: '#1a0a0a', border: '1px solid #4a1a1a', color: 'white', padding: '8px 12px', borderRadius: '4px', fontSize: '13px', boxSizing: 'border-box' }} />
                        </div>
                        <div>
                            <label style={{ fontSize: '12px', color: '#aaa', display: 'block', marginBottom: '4px' }}>Paralelo</label>
                            <select value={nuevaTarea.paralelo} onChange={e => setNuevaTarea({ ...nuevaTarea, paralelo: e.target.value })}
                                style={{ width: '100%', background: '#1a0a0a', border: '1px solid #4a1a1a', color: 'white', padding: '8px 12px', borderRadius: '4px', fontSize: '13px' }}>
                                <option value="">Seleccionar paralelo...</option>
                                <option value="A">Paralelo A</option>
                                <option value="B">Paralelo B</option>
                            </select>
                        </div>
                        <div>
                            <label style={{ fontSize: '12px', color: '#aaa', display: 'block', marginBottom: '4px' }}>Fecha límite</label>
                            <input type="date" value={nuevaTarea.fecha} onChange={e => setNuevaTarea({ ...nuevaTarea, fecha: e.target.value })}
                                style={{ width: '100%', background: '#1a0a0a', border: '1px solid #4a1a1a', color: 'white', padding: '8px 12px', borderRadius: '4px', fontSize: '13px', boxSizing: 'border-box' }} />
                        </div>
                        <div style={{ display: 'flex', gap: '10px', justifyContent: 'flex-end' }}>
                            <button onClick={() => setMostrarForm(false)}
                                style={{ background: '#333', border: 'none', color: 'white', padding: '8px 20px', borderRadius: '4px', cursor: 'pointer' }}>
                                Cancelar
                            </button>
                            <button style={{ background: '#e05555', border: 'none', color: 'white', padding: '8px 20px', borderRadius: '4px', cursor: 'pointer', fontWeight: '600' }}>
                                Publicar tarea
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {/* Lista de tareas */}
            <div style={{ display: 'flex', flexDirection: 'column', gap: '12px' }}>
                {tareas.map(t => (
                    <div key={t.id} style={{ background: '#2d0f0f', borderRadius: '8px', border: '1px solid #4a1a1a', overflow: 'hidden' }}>
                        <div style={{ padding: '16px', display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                            <div>
                                <div style={{ fontWeight: '600', marginBottom: '4px' }}>{t.nombre}</div>
                                <div style={{ fontSize: '12px', color: '#aaa' }}>Fecha límite: {t.fecha}</div>
                                <div style={{ fontSize: '12px', color: '#aaa', marginTop: '4px' }}>
                                     {t.entregado} entregaron ·  {t.pendiente} pendientes
                                </div>
                            </div>
                            <button onClick={() => setExpandida(expandida === t.id ? null : t.id)}
                                style={{ background: '#8b1a1a', border: 'none', color: 'white', padding: '8px 14px', borderRadius: '4px', cursor: 'pointer', fontSize: '12px' }}>
                                {expandida === t.id ? 'Cerrar ▲' : 'Ver entregas ▼'}
                            </button>
                        </div>

                        {expandida === t.id && (
                            <div style={{ borderTop: '1px solid #4a1a1a', padding: '16px', background: '#1a0a0a' }}>
                                <div style={{ fontSize: '13px', color: '#aaa', marginBottom: '10px' }}>Estudiantes que entregaron:</div>
                                {entregados[t.id]?.length > 0 ? (
                                    entregados[t.id].map((nombre, i) => (
                                        <div key={i} style={{ display: 'flex', alignItems: 'center', gap: '8px', padding: '8px 0', borderBottom: '1px solid #2a1010' }}>
                                            <div style={{ width: '24px', height: '24px', borderRadius: '50%', background: '#8b1a1a', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: '11px' }}>
                                                {nombre[0]}
                                            </div>
                                            <span style={{ fontSize: '13px' }}>{nombre}</span>
                                            <span style={{ marginLeft: 'auto', fontSize: '11px', background: '#1a4a1a', color: '#4caf50', padding: '2px 8px', borderRadius: '10px' }}>Entregado</span>
                                        </div>
                                    ))
                                ) : (
                                    <div style={{ color: '#555', fontSize: '13px' }}>Ningún estudiante ha entregado aún.</div>
                                )}
                            </div>
                        )}
                    </div>
                ))}
            </div>
        </DocenteLayout>
    )
}