 import DocenteLayout from '../../Components/DocenteLayout'
import { useState } from 'react'

const EstadoBadge = ({ estado }) => {
    const colores = {
        'Entregado':   { bg: '#1a4a1a', color: '#4caf50' },
        'En revisión': { bg: '#4a3a00', color: '#ffc107' },
        'Pendiente':   { bg: '#4a1a1a', color: '#e05555' },
        'Sin asignar': { bg: '#2a2a2a', color: '#aaa' },
    }
    const c = colores[estado] || colores['Sin asignar']
    return (
        <span style={{ background: c.bg, color: c.color, padding: '3px 10px', borderRadius: '12px', fontSize: '11px', fontWeight: '600' }}>
            {estado}
        </span>
    )
}

export default function GestionProyectos({ estudiantes, tareas, stats }) {
    const [busqueda, setBusqueda] = useState('')

    const filtrados = estudiantes.filter(e =>
        e.nombre.toLowerCase().includes(busqueda.toLowerCase())
    )

    return (
        <DocenteLayout active="GESTIONAR PROYECTOS">
            <h1 style={{ fontSize: '28px', fontWeight: 'bold', marginBottom: '4px' }}>GESTIONAR PROYECTOS</h1>
            <div style={{ color: '#aaa', fontSize: '13px', marginBottom: '24px' }}>Talleres | Paralelo A - Gestión 2027-I</div>

            {/* Stats */}
            <div style={{ display: 'flex', gap: '16px', marginBottom: '30px', flexWrap: 'wrap' }}>
                {[
                    { value: stats.total_estudiantes, label: 'Estudiantes' },
                    { value: stats.tareas_activas,    label: 'Tareas activas' },
                    { value: stats.sin_tutor_jurado,  label: 'Sin tutor/Jurado', color: '#c0392b' },
                    { value: stats.entregas_hoy,      label: 'Entregas hoy' },
                ].map(s => (
                    <div key={s.label} style={{ background: s.color || '#8b1a1a', borderRadius: '8px', padding: '20px', minWidth: '120px', textAlign: 'center' }}>
                        <div style={{ fontSize: '36px', fontWeight: 'bold' }}>{s.value}</div>
                        <div style={{ fontSize: '11px', color: '#ddd', marginTop: '4px' }}>{s.label}</div>
                    </div>
                ))}
            </div>

            {/* Tareas */}
            <div style={{ marginBottom: '30px' }}>
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '12px' }}>
                    <span style={{ fontWeight: '600', fontSize: '15px' }}>Tareas de revisión</span>
                    <button style={{ background: '#8b1a1a', border: 'none', color: 'white', padding: '8px 16px', borderRadius: '4px', cursor: 'pointer', fontSize: '12px' }}>
                        + NUEVA TAREA
                    </button>
                </div>
                {tareas.map(t => (
                    <div key={t.id} style={{ background: '#2d0f0f', borderRadius: '8px', padding: '16px', marginBottom: '12px', borderLeft: `4px solid ${t.estado === 'activa' ? '#e05555' : '#555'}` }}>
                        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                            <span style={{ fontWeight: '600' }}>{t.nombre}</span>
                            <span style={{ fontSize: '11px', background: t.estado === 'activa' ? '#8b1a1a' : '#333', padding: '3px 10px', borderRadius: '12px' }}>
                                {t.estado}
                            </span>
                        </div>
                        <div style={{ fontSize: '12px', color: '#aaa', marginTop: '4px' }}>Fecha límite: {t.fecha}</div>
                        <div style={{ background: '#4a1a1a', borderRadius: '4px', height: '6px', margin: '10px 0' }}>
                            <div style={{ background: '#e05555', height: '6px', borderRadius: '4px', width: `${(t.entregado / 24) * 100}%` }} />
                        </div>
                        <div style={{ fontSize: '11px', color: '#aaa' }}>
                            Entregado: {t.entregado} · Pendiente: {t.pendiente} · Revisado: {t.revisado}
                        </div>
                    </div>
                ))}
            </div>

            {/* Buscador */}
            <div style={{ marginBottom: '16px' }}>
                <input
                    value={busqueda}
                    onChange={e => setBusqueda(e.target.value)}
                    placeholder="Buscar estudiante o proyecto..."
                    style={{ width: '300px', background: '#2d0f0f', border: '1px solid #4a1a1a', color: 'white', padding: '10px 14px', borderRadius: '6px', fontSize: '13px' }}
                />
            </div>

            {/* Tabla estudiantes/proyectos */}
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '12px' }}>
                <span style={{ fontWeight: '600', fontSize: '15px' }}>Mis estudiantes</span>
                <button style={{ background: '#8b1a1a', border: 'none', color: 'white', padding: '8px 16px', borderRadius: '4px', cursor: 'pointer', fontSize: '12px' }}>
                    + AGREGAR ESTUDIANTE
                </button>
            </div>
            <div style={{ background: '#2d0f0f', borderRadius: '8px', overflow: 'hidden' }}>
                <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: '13px' }}>
                    <thead>
                        <tr style={{ borderBottom: '1px solid #4a1a1a' }}>
                            {['Estudiante','Tutor','Jurados','Estado Rev1','Proyecto'].map(h => (
                                <th key={h} style={{ padding: '12px 16px', textAlign: 'left', color: '#aaa', fontWeight: '600', fontSize: '12px' }}>{h}</th>
                            ))}
                        </tr>
                    </thead>
                    <tbody>
                        {filtrados.map(est => (
                            <tr key={est.id} style={{ borderBottom: '1px solid #3a1515' }}>
                                <td style={{ padding: '12px 16px' }}>
                                    <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
                                        <div style={{ width: '28px', height: '28px', borderRadius: '50%', background: '#8b1a1a', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: '12px' }}>
                                            {est.nombre[0]}
                                        </div>
                                        {est.nombre}
                                    </div>
                                </td>
                                <td style={{ padding: '12px 16px', color: '#ddd' }}>{est.tutor || '-'}</td>
                                <td style={{ padding: '12px 16px', color: '#ddd' }}>{est.jurados || '-'}</td>
                                <td style={{ padding: '12px 16px' }}><EstadoBadge estado={est.estado} /></td>
                                <td style={{ padding: '12px 16px' }}>
                                    <input
                                        placeholder="Nombre del proyecto..."
                                        style={{ background: '#1a0a0a', border: '1px solid #4a1a1a', color: 'white', padding: '6px 10px', borderRadius: '4px', fontSize: '12px', width: '180px' }}
                                    />
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </DocenteLayout>
    )
}