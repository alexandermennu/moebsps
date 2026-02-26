import { useState, useEffect } from 'react';
import api from '../../api/client';
import StatCard from '../../components/StatCard';
import { StatusBadge, formatDate } from '../../utils/helpers';
import {
  Activity, AlertTriangle, Repeat, TrendingUp,
  BarChart3
} from 'lucide-react';
import {
  BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip,
  ResponsiveContainer, PieChart, Pie, Cell, Legend
} from 'recharts';

const PIE_COLORS = ['#3b82f6', '#22c55e', '#ef4444', '#f59e0b'];

export default function MinisterDashboard() {
  const [data, setData] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    async function fetch() {
      try {
        const res = await api.get('/dashboard/minister');
        setData(res);
      } catch (err) {
        console.error(err);
      } finally {
        setLoading(false);
      }
    }
    fetch();
  }, []);

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-10 w-10 border-b-2 border-primary-600" />
      </div>
    );
  }

  if (!data) return <p className="text-gray-500">Failed to load dashboard.</p>;

  const s = data.summary || {};
  const pieData = [
    { name: 'Active', value: parseInt(s.total_active) || 0 },
    { name: 'Completed', value: parseInt(s.total_completed) || 0 },
    { name: 'Overdue', value: parseInt(s.total_overdue) || 0 },
    { name: 'Repeated', value: parseInt(s.total_repeated) || 0 },
  ].filter((d) => d.value > 0);

  const perfData = (data.divisionPerformance || []).map((d) => ({
    name: d.division_name?.length > 12 ? d.division_name.substring(0, 12) + '…' : d.division_name,
    'Completion %': parseInt(d.completion_pct) || 0,
    Overdue: parseInt(d.overdue) || 0,
  }));

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-gray-900">Minister Dashboard</h1>
        <p className="text-gray-500 mt-1">Executive overview of bureau activities</p>
      </div>

      {/* Stats */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <StatCard
          title="Total Active"
          value={s.total_active || 0}
          icon={Activity}
          color="blue"
        />
        <StatCard
          title="Total Overdue"
          value={s.total_overdue || 0}
          icon={AlertTriangle}
          color="red"
        />
        <StatCard
          title="Repeated Activities"
          value={s.total_repeated || 0}
          icon={Repeat}
          color="yellow"
        />
        <StatCard
          title="Total Completed"
          value={s.total_completed || 0}
          subtitle={`of ${s.grand_total || 0} total`}
          icon={TrendingUp}
          color="green"
        />
      </div>

      {/* Charts */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Activity Distribution Pie */}
        <div className="card p-5">
          <h2 className="font-semibold text-gray-800 mb-4">Activity Distribution</h2>
          {pieData.length > 0 ? (
            <ResponsiveContainer width="100%" height={280}>
              <PieChart>
                <Pie
                  data={pieData}
                  dataKey="value"
                  nameKey="name"
                  cx="50%"
                  cy="50%"
                  outerRadius={100}
                  label={({ name, value }) => `${name}: ${value}`}
                >
                  {pieData.map((_, i) => (
                    <Cell key={i} fill={PIE_COLORS[i % PIE_COLORS.length]} />
                  ))}
                </Pie>
                <Legend />
                <Tooltip />
              </PieChart>
            </ResponsiveContainer>
          ) : (
            <p className="text-gray-400 text-center py-12">No data available</p>
          )}
        </div>

        {/* Division Performance */}
        <div className="card p-5">
          <h2 className="font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <BarChart3 size={18} />
            Division Performance
          </h2>
          {perfData.length > 0 ? (
            <ResponsiveContainer width="100%" height={280}>
              <BarChart data={perfData}>
                <CartesianGrid strokeDasharray="3 3" stroke="#f0f0f0" />
                <XAxis dataKey="name" tick={{ fontSize: 11 }} />
                <YAxis tick={{ fontSize: 12 }} />
                <Tooltip />
                <Bar dataKey="Completion %" fill="#22c55e" radius={[2, 2, 0, 0]} />
                <Bar dataKey="Overdue" fill="#ef4444" radius={[2, 2, 0, 0]} />
              </BarChart>
            </ResponsiveContainer>
          ) : (
            <p className="text-gray-400 text-center py-12">No data available</p>
          )}
        </div>
      </div>

      {/* Overdue Activities */}
      {data.overdueActivities?.length > 0 && (
        <div className="card">
          <div className="px-5 py-4 border-b border-gray-100">
            <h2 className="font-semibold text-red-700 flex items-center gap-2">
              <AlertTriangle size={18} />
              Overdue Activities ({data.overdueActivities.length})
            </h2>
          </div>
          <div className="overflow-x-auto">
            <table className="w-full text-sm">
              <thead className="bg-gray-50">
                <tr>
                  <th className="text-left px-5 py-3 font-medium text-gray-600">Activity</th>
                  <th className="text-left px-5 py-3 font-medium text-gray-600">Division</th>
                  <th className="text-left px-5 py-3 font-medium text-gray-600">Assigned To</th>
                  <th className="text-left px-5 py-3 font-medium text-gray-600">Deadline</th>
                  <th className="text-left px-5 py-3 font-medium text-gray-600">Status</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-100">
                {data.overdueActivities.map((act) => (
                  <tr key={act.id} className="hover:bg-red-50/50">
                    <td className="px-5 py-3 font-medium text-gray-900">{act.title}</td>
                    <td className="px-5 py-3 text-gray-600">{act.division_name}</td>
                    <td className="px-5 py-3 text-gray-600">
                      {(act.assigned_users || []).join(', ') || '—'}
                    </td>
                    <td className="px-5 py-3 text-gray-600">{formatDate(act.deadline)}</td>
                    <td className="px-5 py-3"><StatusBadge status={act.status} /></td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      )}

      {/* Repeated Activities */}
      {data.repeatedActivities?.length > 0 && (
        <div className="card">
          <div className="px-5 py-4 border-b border-gray-100">
            <h2 className="font-semibold text-yellow-700 flex items-center gap-2">
              <Repeat size={18} />
              Repeated Activities ({data.repeatedActivities.length})
            </h2>
          </div>
          <div className="overflow-x-auto">
            <table className="w-full text-sm">
              <thead className="bg-gray-50">
                <tr>
                  <th className="text-left px-5 py-3 font-medium text-gray-600">Activity</th>
                  <th className="text-left px-5 py-3 font-medium text-gray-600">Division</th>
                  <th className="text-left px-5 py-3 font-medium text-gray-600">Carried Forward</th>
                  <th className="text-left px-5 py-3 font-medium text-gray-600">Status</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-100">
                {data.repeatedActivities.map((act) => (
                  <tr key={act.id} className="hover:bg-yellow-50/50">
                    <td className="px-5 py-3 font-medium text-gray-900">{act.title}</td>
                    <td className="px-5 py-3 text-gray-600">{act.division_name}</td>
                    <td className="px-5 py-3">
                      <span className="badge-purple">{act.carry_forward_count} weeks</span>
                    </td>
                    <td className="px-5 py-3"><StatusBadge status={act.status} /></td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      )}

      {/* By Responsible Person */}
      {data.byResponsiblePerson?.length > 0 && (
        <div className="card">
          <div className="px-5 py-4 border-b border-gray-100">
            <h2 className="font-semibold text-gray-800">Activity by Responsible Person</h2>
          </div>
          <div className="overflow-x-auto">
            <table className="w-full text-sm">
              <thead className="bg-gray-50">
                <tr>
                  <th className="text-left px-5 py-3 font-medium text-gray-600">Person</th>
                  <th className="text-left px-5 py-3 font-medium text-gray-600">Total</th>
                  <th className="text-left px-5 py-3 font-medium text-gray-600">Completed</th>
                  <th className="text-left px-5 py-3 font-medium text-gray-600">Overdue</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-100">
                {data.byResponsiblePerson.map((p, i) => (
                  <tr key={i} className="hover:bg-gray-50">
                    <td className="px-5 py-3 font-medium text-gray-900">{p.person}</td>
                    <td className="px-5 py-3 text-gray-600">{p.total}</td>
                    <td className="px-5 py-3"><span className="badge-green">{p.completed}</span></td>
                    <td className="px-5 py-3">
                      {parseInt(p.overdue) > 0
                        ? <span className="badge-red">{p.overdue}</span>
                        : <span className="text-gray-400">0</span>
                      }
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      )}
    </div>
  );
}
