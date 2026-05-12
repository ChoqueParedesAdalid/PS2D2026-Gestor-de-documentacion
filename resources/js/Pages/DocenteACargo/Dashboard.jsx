import DocenteLayout from '../../Components/DocenteLayout'

const StatCard = ({ value, label, color }) => (
    <div style={{ background: color || '#8b1a1a', borderRadius: '8px', padding: '20px', minWidth: '130px', textAlign: 'center' }}>
        <div style={{ fontSize: '36px', fontWeight: 'bold' }}>{value}</div>
        <div style={{ fontSize: '11px', color: '#ddd', marginTop: '4px' }}>{label}</div>
    </div>
)

export default function Dashboard({ tareas, stats }) {
    return (
        <DocenteLayout active="INICIO">
            <h1 style={{ fontSize: '28px', fontWeight: 'bold', marginBottom: '4px' }}>DOCENTE A CARGO</h1>
            <div style={{ color: '#aaa', fontSize: '13px', marginBottom: '24px' }}>Talleres | Paralelo A - Gestión 2027-I</div>

            {/* Stats */}
            <div style={{ display: 'flex', gap: '16px', marginBottom: '30px', flexWrap: 'wrap' }}>
                <StatCard value={stats.total_estudiantes} label="Estudiantes" />
                <StatCard value={stats.tareas_activas}    label="Tareas activas" />
                <StatCard value={stats.sin_tutor_jurado}  label="Sin tutor/Jurado" color="#c0392b" />
                <StatCard value={stats.entregas_hoy}      label="Entregas hoy" />
            </div>

            {/* Tareas como estadísticas */}
            <div>
                <span style={{ fontWeight: '600', fontSize: '15px' }}>Tareas de revisión</span>
                <div style={{ marginTop: '12px', display: 'flex', flexDirection: 'column', gap: '12px' }}>
                    {tareas.map(t => (
                        <div key={t.id} style={{ background: '#2d0f0f', borderRadius: '8px', padding: '16px', borderLeft: `4px solid ${t.estado === 'activa' ? '#e05555' : '#555'}` }}>
                            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                                <span style={{ fontWeight: '600' }}>{t.nombre}</span>
                                <span style={{ fontSize: '11px', background: t.estado === 'activa' ? '#8b1a1a' : '#333', padding: '3px 10px', borderRadius: '12px' }}>
                                    {t.estado === 'activa' ? 'Activa' : 'En espera'}
                                </span>
                            </div>
                            <div style={{ fontSize: '12px', color: '#aaa', marginTop: '4px' }}>Fecha límite: {t.fecha}</div>
                            <div style={{ background: '#4a1a1a', borderRadius: '4px', height: '8px', margin: '12px 0 6px' }}>
                                <div style={{ background: '#e05555', height: '8px', borderRadius: '4px', width: `${(t.entregado / stats.total_estudiantes) * 100}%`, transition: 'width 0.3s' }} />
                            </div>
                            <div style={{ display: 'flex', gap: '16px', fontSize: '12px', color: '#aaa' }}>
                                <span> Entregado: <strong style={{ color: '#4caf50' }}>{t.entregado}</strong></span>
                                <span> Pendiente: <strong style={{ color: '#ffc107' }}>{t.pendiente}</strong></span>
                                <span> Revisado: <strong style={{ color: '#64b5f6' }}>{t.revisado}</strong></span>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </DocenteLayout>
    )
}