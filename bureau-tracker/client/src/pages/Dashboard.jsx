import { useAuth } from '../context/AuthContext';
import DirectorDashboard from './dashboard/DirectorDashboard';
import BureauHeadDashboard from './dashboard/BureauHeadDashboard';
import MinisterDashboard from './dashboard/MinisterDashboard';

export default function Dashboard() {
  const { user } = useAuth();

  switch (user?.role) {
    case 'division_director':
      return <DirectorDashboard />;
    case 'bureau_head':
      return <BureauHeadDashboard />;
    case 'minister':
      return <MinisterDashboard />;
    case 'admin':
      return <BureauHeadDashboard />; // Admin sees bureau-head view
    default:
      return <p>Unknown role</p>;
  }
}
