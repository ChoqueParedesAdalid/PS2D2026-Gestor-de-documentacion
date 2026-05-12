import { Link, router } from '@inertiajs/react'

export default function DocenteLayout({ children, active }) {
    const handleLogout = () => {
        router.post('/logout')
    }

    return (
        <div style={{ display: 'flex', minHeight: '100vh', background: '#1a0a0a', color: 'white', fontFamily: 'sans-serif', margin: 0, padding: 0 }}>
            {/* Sidebar */}
            <div style={{ width: '220px', background: '#2d0f0f', display: 'flex', flexDirection: 'column', justifyContent: 'space-between', flexShrink: 0 }}>
                <div>
                    <div style={{ padding: '20px 20px 30px' }}>
                        <div style={{ fontSize: '12px', color: '#aaa' }}>DocGest · Univalle</div>
                        <div style={{ fontSize: '11px', color: '#e05555', marginTop: '2px' }}>● UNIVALLE</div>
                    </div>
                    <nav>
                        {[
                            { label: 'INICIO',                href: '/docente/dashboard' },
                            { label: 'GESTIONAR ESTUDIANTES', href: '/docente/estudiantes' },
                            { label: 'GESTIONAR TAREAS',      href: '/docente/tareas' },
                        ].map(item => (
                            <Link key={item.label} href={item.href} style={{
                                display: 'block', padding: '14px 20px', fontSize: '12px', fontWeight: '600',
                                letterSpacing: '0.5px', color: active === item.label ? 'white' : '#aaa',
                                background: active === item.label ? '#8b1a1a' : 'transparent',
                                textDecoration: 'none', borderLeft: active === item.label ? '3px solid #e05555' : '3px solid transparent',
                            }}>
                                {item.label}
                            </Link>
                        ))}
                    </nav>
                </div>
                <button onClick={handleLogout} style={{ margin: '20px', padding: '10px', background: 'transparent', border: '1px solid #555', color: '#aaa', cursor: 'pointer', fontSize: '12px', borderRadius: '4px' }}>
                    SALIR
                </button>
            </div>

            {/* Main */}
            <div style={{ flex: 1, display: 'flex', flexDirection: 'column', minWidth: 0 }}>
                {/* Top bar */}
                <div style={{ background: '#2d0f0f', padding: '10px 30px', display: 'flex', justifyContent: 'flex-end', alignItems: 'center', borderBottom: '1px solid #4a1a1a' }}>
                    <div style={{ width: '32px', height: '32px', borderRadius: '50%', background: '#e05555', display: 'flex', alignItems: 'center', justifyContent: 'center', fontWeight: 'bold', fontSize: '14px' }}>
                        D
                    </div>
                </div>
                <div style={{ flex: 1, padding: '30px', overflowY: 'auto', boxSizing: 'border-box' }}>
                    {children}
                </div>
            </div>
        </div>
    )
}