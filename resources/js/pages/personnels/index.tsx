import AppLayout from '@/layouts/app-layout';
import { index, create, destroy } from '@/routes/personnels';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Edit, Trash2, Plus, Eye } from 'lucide-react';
import { useState } from 'react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Personnels',
        href: index().url,
    },
];

interface Personnel {
    uuid: string;
    name: string;
    contact_number: string;
    serial_number: string;
    department: string;
    created_at: string;
}

interface PaginatedPersonnels {
    data: Personnel[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

interface IndexProps {
    personnels: PaginatedPersonnels;
}

export default function Index({ personnels }: IndexProps) {
    const [deleteDialogOpen, setDeleteDialogOpen] = useState(false);
    const [personnelToDelete, setPersonnelToDelete] = useState<string | null>(null);

    const handleDeleteClick = (uuid: string) => {
        setPersonnelToDelete(uuid);
        setDeleteDialogOpen(true);
    };

    const confirmDelete = () => {
        if (personnelToDelete) {
            router.delete(destroy({ personnel: personnelToDelete }).url);
            setDeleteDialogOpen(false);
            setPersonnelToDelete(null);
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Personnels" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <Card>
                    <CardHeader>
                        <div className="flex items-center justify-between">
                            <div>
                                <CardTitle>Personnels</CardTitle>
                                <CardDescription>
                                    Manage personnel information
                                </CardDescription>
                            </div>
                            <Link href={create().url}>
                                <Button>
                                    <Plus className="mr-2 h-4 w-4" />
                                    Create New
                                </Button>
                            </Link>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div className="rounded-md border">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Name</TableHead>
                                        <TableHead>Serial Number</TableHead>
                                        <TableHead>Department</TableHead>
                                        <TableHead>Contact Number</TableHead>
                                        <TableHead className="text-right">Actions</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {personnels.data.length === 0 ? (
                                        <TableRow>
                                            <TableCell
                                                colSpan={5}
                                                className="h-24 text-center"
                                            >
                                                No personnels found.
                                            </TableCell>
                                        </TableRow>
                                    ) : (
                                        personnels.data.map((personnel) => (
                                            <TableRow key={personnel.uuid}>
                                                <TableCell className="font-medium">
                                                    {personnel.name}
                                                </TableCell>
                                                <TableCell>{personnel.serial_number}</TableCell>
                                                <TableCell>{personnel.department}</TableCell>
                                                <TableCell>{personnel.contact_number}</TableCell>
                                                <TableCell className="text-right">
                                                    <div className="flex justify-end gap-2">
                                                        <Link
                                                            href={`/personnels/${personnel.uuid}`}
                                                        >
                                                            <Button
                                                                variant="outline"
                                                                size="sm"
                                                            >
                                                                <Eye className="h-4 w-4" />
                                                            </Button>
                                                        </Link>
                                                        <Link
                                                            href={`/personnels/${personnel.uuid}/edit`}
                                                        >
                                                            <Button
                                                                variant="outline"
                                                                size="sm"
                                                            >
                                                                <Edit className="h-4 w-4" />
                                                            </Button>
                                                        </Link>
                                                        <Button
                                                            variant="destructive"
                                                            size="sm"
                                                            onClick={() =>
                                                                handleDeleteClick(personnel.uuid)
                                                            }
                                                        >
                                                            <Trash2 className="h-4 w-4" />
                                                        </Button>
                                                    </div>
                                                </TableCell>
                                            </TableRow>
                                        ))
                                    )}
                                </TableBody>
                            </Table>
                        </div>
                        {personnels.last_page > 1 && (
                            <div className="mt-4 flex items-center justify-between">
                                <div className="text-sm text-muted-foreground">
                                    Showing {(personnels.current_page - 1) * personnels.per_page + 1} to{' '}
                                    {Math.min(personnels.current_page * personnels.per_page, personnels.total)} of{' '}
                                    {personnels.total} results
                                </div>
                                <div className="flex gap-2">
                                    {personnels.current_page > 1 && (
                                        <Link
                                            href={index({ query: { page: personnels.current_page - 1 } }).url}
                                        >
                                            <Button variant="outline">Previous</Button>
                                        </Link>
                                    )}
                                    {personnels.current_page < personnels.last_page && (
                                        <Link
                                            href={index({ query: { page: personnels.current_page + 1 } }).url}
                                        >
                                            <Button variant="outline">Next</Button>
                                        </Link>
                                    )}
                                </div>
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>

            {/* Delete Confirmation Dialog */}
            <Dialog open={deleteDialogOpen} onOpenChange={setDeleteDialogOpen}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Are you absolutely sure?</DialogTitle>
                        <DialogDescription>
                            This action cannot be undone. This will permanently delete the
                            personnel record.
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter>
                        <Button
                            variant="outline"
                            onClick={() => setDeleteDialogOpen(false)}
                        >
                            Cancel
                        </Button>
                        <Button variant="destructive" onClick={confirmDelete}>
                            Delete
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
