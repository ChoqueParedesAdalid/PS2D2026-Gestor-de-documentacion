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
    return <span style={{ background: c.bg, color: c.color, padding: '3px 10px', borderRadius: '12px', fontSize: '11px', fontWeight: '600' }}>{estado}</span>
}

export default function GestionEstudiantes({ estudiantes }) {
    const [busqueda, setBusqueda] = useState('')
    const [seleccionado, setSeleccionado] = useState(null)
    const [tutor, setTutor] = useState('')
    const [jurado1, setJurado1] = useState('')
    const [jurado2, setJurado2] = useState('')
    const [proyecto, setProyecto] = useState('')
    const [buscarTutor, setBuscarTutor] = useState('')
    const [buscarJurado1, setBuscarJurado1] = useState('')
    const [buscarJurado2, setBuscarJurado2] = useState('')

    const docentes = ['Lic. Marcus Flores', 'Lic. Fabian Estapm', 'Lic. Baila Sin Cesar', 'Lic. Lima', 'Lic. Hinojosa']

    const filtrarDocentes = (texto) => docentes.filter(d => d.toLowerCase().includes(texto.toLowerCase()))

    const filtrados = estudiantes.filter(e =>
        e.nombre.toLowerCase().includes(busqueda.toLowerCase())
    )

    const seleccionar = (est) => {
        setSeleccionado(est)
        setTutor(est.tutor || '')
        setProyecto(est.proyecto || '')
    }

    return (
        <DocenteLayout active="GESTIONAR ESTUDIANTES">
            <h1 style={{ fontSize: '28px', fontWeight: 'bold', marginBottom: '4px' }}>GESTIÓN DE ESTUDIANTES</h1>
            <div style={{ color: '#aaa', fontSize: '13px', marginBottom: '24px' }}>Talleres | Paralelo A - Gestión 2027-I</div>

            {/* Panel asignar - siempre visible */}
            <div style={{ background: '#2d0f0f', borderRadius: '12px', padding: '24px', border: '1px solid #4a1a1a', marginBottom: '24px' }}>
                <h3 style={{ marginBottom: '16px', fontSize: '15px' }}>
                    Asignar tutor y tribunal a:{' '}
                    <span style={{ color: '#e05555' }}>{seleccionado ? seleccionado.nombre : '— selecciona un estudiante —'}</span>
                </h3>

                {/* Nombre proyecto */}
                <div style={{ marginBottom: '14px' }}>
                    <label style={{ fontSize: '12px', color: '#aaa', display: 'block', marginBottom: '6px' }}>Nombre del proyecto</label>
                    <input
                        value={proyecto}
                        onChange={e => setProyecto(e.target.value)}
                        placeholder="Ej: Sistema de gestión académica..."
                        disabled={!seleccionado}
                        style={{ width: '100%', background: '#1a0a0a', border: '1px solid #4a1a1a', color: 'white', padding: '8px 12px', borderRadius: '4px', fontSize: '13px', boxSizing: 'border-box', opacity: seleccionado ? 1 : 0.5 }}
                    />
                </div>

                {/* Tutor */}
                {[
                    { label: 'Tutor',      buscar: buscarTutor,   setBuscar: setBuscarTutor,   value: tutor,   setValue: setTutor },
                    { label: 'Tribunal 1', buscar: buscarJurado1, setBuscar: setBuscarJurado1, value: jurado1, setValue: setJurado1 },
                    { label: 'Tribunal 2', buscar: buscarJurado2, setBuscar: setBuscarJurado2, value: jurado2, setValue: setJurado2 },
                ].map(f => (
                    <div key={f.label} style={{ marginBottom: '14px', position: 'relative' }}>
                        <label style={{ fontSize: '12px', color: '#aaa', display: 'block', marginBottom: '6px' }}>{f.label}</label>
                        <div style={{ display: 'flex', gap: '8px' }}>
                            <div style={{ flex: 1, position: 'relative' }}>
                                <input
                                    value={f.buscar}
                                    onChange={e => { f.setBuscar(e.target.value); f.setValue('') }}
                                    placeholder={`Buscar ${f.label.toLowerCase()}...`}
                                    disabled={!seleccionado}
                                    style={{ width: '100%', background: '#1a0a0a', border: '1px solid #4a1a1a', color: 'white', padding: '8px 12px', borderRadius: '4px', fontSize: '13px', boxSizing: 'border-box', opacity: seleccionado ? 1 : 0.5 }}
                                />
                                {f.buscar && filtrarDocentes(f.buscar).length > 0 && (
                                    <div style={{ position: 'absolute', top: '100%', left: 0, right: 0, background: '#1a0a0a', border: '1px solid #4a1a1a', borderRadius: '4px', zIndex: 100 }}>
                                        {filtrarDocentes(f.buscar).map(d => (
                                            <div key={d} onClick={() => { f.setValue(d); f.setBuscar(d) }}
                                                style={{ padding: '8px 12px', cursor: 'pointer', fontSize: '13px', borderBottom: '1px solid #2a1010' }}
                                                onMouseEnter={e => e.target.style.background = '#2d0f0f'}
                                                onMouseLeave={e => e.target.style.background = 'transparent'}>
                                                {d}
                                            </div>
                                        ))}
                                    </div>
                                )}
                            </div>
                            <button style={{ background: '#e05555', border: 'none', color: 'white', padding: '8px 16px', borderRadius: '4px', cursor: 'pointer', fontSize: '12px', whiteSpace: 'nowrap' }}>
                                AGREGAR
                            </button>
                        </div>
                    </div>
                ))}

                <div style={{ display: 'flex', justifyContent: 'flex-end', marginTop: '8px' }}>
                    <button style={{ background: '#e05555', border: 'none', color: 'white', padding: '10px 24px', borderRadius: '4px', cursor: 'pointer', fontSize: '13px', fontWeight: '600' }}>
                        GUARDAR
                    </button>
                </div>
            </div>

            {/* Buscador */}
            <div style={{ marginBottom: '16px' }}>
                <input
                    value={busqueda}
                    onChange={e => setBusqueda(e.target.value)}
                    placeholder="Buscar por nombre o apellido..."
                    style={{ width: '300px', background: '#2d0f0f', border: '1px solid #4a1a1a', color: 'white', padding: '10px 14px', borderRadius: '6px', fontSize: '13px' }}
                />
            </div>

            {/* Tabla */}
            <div style={{ background: '#2d0f0f', borderRadius: '8px', overflow: 'hidden' }}>
                <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: '13px' }}>
                    <thead>
                        <tr style={{ borderBottom: '1px solid #4a1a1a' }}>
                            {['Estudiante','Tutor','Tribunal 1','Tribunal 2','Estado'].map(h => (
                                <th key={h} style={{ padding: '12px 16px', textAlign: 'left', color: '#aaa', fontWeight: '600', fontSize: '12px' }}>{h}</th>
                            ))}
                            <th style={{ padding: '12px 16px' }}></th>
                        </tr>
                    </thead>
                    <tbody>
                        {filtrados.map(est => (
                            <tr key={est.id}
                                onClick={() => seleccionar(est)}
                                style={{ borderBottom: '1px solid #3a1515', cursor: 'pointer', background: seleccionado?.id === est.id ? '#3d1515' : 'transparent' }}>
                                <td style={{ padding: '12px 16px' }}>
                                    <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
                                        <div style={{ width: '28px', height: '28px', borderRadius: '50%', background: seleccionado?.id === est.id ? '#e05555' : '#8b1a1a', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: '12px' }}>
                                            {est.nombre[0]}
                                        </div>
                                        <span style={{ color: seleccionado?.id === est.id ? 'white' : '#ddd' }}>{est.nombre}</span>
                                    </div>
                                </td>
                                <td style={{ padding: '12px 16px', color: '#ddd' }}>{est.tutor || '-'}</td>
                                <td style={{ padding: '12px 16px', color: '#ddd' }}>{est.jurado1 || '-'}</td>
                                <td style={{ padding: '12px 16px', color: '#ddd' }}>{est.jurado2 || '-'}</td>
                                <td style={{ padding: '12px 16px' }}><EstadoBadge estado={est.estado} /></td>
                                <td style={{ padding: '12px 16px' }}>
                                    <button style={{ background: '#333', border: 'none', color: 'white', padding: '5px 12px', borderRadius: '4px', cursor: 'pointer', fontSize: '11px' }}>
                                        Ver doc.
                                    </button>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </DocenteLayout>
    )
}